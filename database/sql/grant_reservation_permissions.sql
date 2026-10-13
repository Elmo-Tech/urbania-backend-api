-- Run on the application database before deploying reservation permission checks.
-- Grants all five permissions to EVERY existing api role, including limitata
-- and superLimitata. Existing permissions and role assignments are preserved.
-- Safe to rerun. Roles created later require their own assignment or a rerun.
START TRANSACTION;

INSERT INTO permissions (name, guard_name, created_at, updated_at)
SELECT requested.name, 'api', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM (
    SELECT 'allReservation' AS name
    UNION ALL SELECT 'createReservation'
    UNION ALL SELECT 'editReservation'
    UNION ALL SELECT 'updateReservation'
    UNION ALL SELECT 'deleteReservation'
) AS requested
WHERE NOT EXISTS (
    SELECT 1 FROM permissions AS existing
    WHERE existing.name = requested.name AND existing.guard_name = 'api'
);

INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id
FROM roles AS r
JOIN permissions AS p ON p.guard_name = r.guard_name
WHERE r.guard_name = 'api'
  AND p.name IN (
      'allReservation', 'createReservation', 'editReservation',
      'updateReservation', 'deleteReservation'
  )
  AND NOT EXISTS (
      SELECT 1 FROM role_has_permissions AS existing
      WHERE existing.permission_id = p.id AND existing.role_id = r.id
  );

COMMIT;

-- Then run on the application server: php artisan permission:cache-reset
-- Verification: each existing api role should have five reservation permissions.
SELECT r.name AS role_name, p.name AS permission_name
FROM role_has_permissions AS rp
JOIN roles AS r ON r.id = rp.role_id
JOIN permissions AS p ON p.id = rp.permission_id
WHERE r.guard_name = 'api' AND p.guard_name = 'api'
  AND p.name IN (
      'allReservation', 'createReservation', 'editReservation',
      'updateReservation', 'deleteReservation'
  )
ORDER BY r.name, p.name;
