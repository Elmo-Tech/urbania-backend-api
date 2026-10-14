<?php

namespace Tests\Feature;

use App\Models\Reservation\Reservation;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ReservationPermissionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'reservation_permissions_test',
            'database.connections.reservation_permissions_test' => [
                'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
            ],
            'jwt.secret' => str_repeat('test-key', 8),
        ]);
        Mail::fake();
        require_once database_path('migrations/2023_08_01_124726_create_permission_tables.php');
        (new \CreatePermissionTables)->up();
        foreach (['superAdmin', 'admin', 'standard', 'limitata', 'superLimitata'] as $name) {
            Role::create(['name' => $name, 'guard_name' => 'api']);
        }
        $this->grantPermissions();

        foreach ([
            'users' => ['username', 'email', 'password', 'status'],
            'clients' => ['company_name', 'firstname', 'lastname'],
            'reservations' => ['firstname', 'lastname', 'cf', 'p_iva', 'ragione_sociale', 'email', 'phone',
                'number', 'delegated_firstname', 'delegated_lastname', 'message', 'date', 'duration',
                'status', 'parameter_id', 'client_id', 'refuse_reason', 'confirmation_token'],
        ] as $name => $columns) {
            Schema::create($name, function (Blueprint $table) use ($columns) {
                $table->id();
                foreach ($columns as $column) {
                    $table->text($column)->nullable();
                }
                $table->timestamps();
                $table->softDeletes();
            });
        }
        DB::table('clients')->insert(['id' => 1, 'company_name' => 'Test municipality']);
        DB::table('users')->insert(['id' => 1, 'username' => 'operator']);
        Reservation::create($this->payload() + ['client_id' => 1]);
    }

    private function grantPermissions(): void
    {
        $sql = file_get_contents(database_path('sql/grant_reservation_permissions.sql'));
        // Only transaction syntax differs; execute the delivered inserts unchanged.
        DB::unprepared(str_replace('START TRANSACTION;', 'BEGIN TRANSACTION;', $sql));
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function payload(): array
    {
        return ['firstname' => 'Test', 'lastname' => 'Customer', 'email' => 'test@example.test', 'phone' => '123456',
            'date' => '2026-11-26 09:00:00', 'duration' => 30, 'status' => 1, 'clientId' => 1];
    }

    private function requestAction(string $action)
    {
        return match ($action) {
            'allReservation' => $this->getJson('/api/v1/reservations'),
            'createReservation' => $this->postJson('/api/v1/reservations/create', $this->payload()),
            'editReservation' => $this->getJson('/api/v1/reservations/edit?reservationId=1'),
            'updateReservation' => $this->putJson('/api/v1/reservations/update', ['reservationId' => 1] + $this->payload()),
            'deleteReservation' => $this->deleteJson('/api/v1/reservations/delete?reservationId=1'),
        };
    }

    public static function actions(): array
    {
        return array_map(fn ($name) => [$name], [
            'allReservation', 'createReservation', 'editReservation', 'updateReservation', 'deleteReservation',
        ]);
    }

    #[DataProvider('actions')]
    public function test_guests_cannot_access_any_admin_action(string $action): void
    {
        $this->requestAction($action)->assertUnauthorized();
        $this->assertSame(1, Reservation::count());
        Mail::assertNothingSent();
    }

    #[DataProvider('actions')]
    public function test_authenticated_users_without_permission_are_denied(string $action): void
    {
        $this->actingAs(User::findOrFail(1), 'api');
        $this->requestAction($action)->assertForbidden();
        $this->assertSame(1, Reservation::count());
        Mail::assertNothingSent();
    }

    #[DataProvider('actions')]
    public function test_each_action_accepts_its_own_permission(string $action): void
    {
        $user = User::findOrFail(1);
        $user->givePermissionTo($action);
        $this->actingAs($user, 'api');
        $this->requestAction($action)->assertOk();
    }

    public function test_read_permission_does_not_allow_modifying_or_deleting(): void
    {
        $user = User::findOrFail(1);
        $user->givePermissionTo('allReservation');
        $this->actingAs($user, 'api');
        $this->requestAction('allReservation')->assertOk();
        foreach (['createReservation', 'editReservation', 'updateReservation', 'deleteReservation'] as $action) {
            $this->requestAction($action)->assertForbidden();
        }
        $this->assertSame(1, Reservation::count());
    }

    public static function roles(): array
    {
        return [['superAdmin'], ['admin'], ['standard'], ['limitata'], ['superLimitata']];
    }

    #[DataProvider('roles')]
    public function test_every_requested_role_can_manage_reservations(string $role): void
    {
        $user = User::findOrFail(1);
        $user->assignRole($role);
        $this->actingAs($user, 'api');
        foreach (self::actions() as [$action]) {
            $this->requestAction($action)->assertOk();
        }
        $this->assertSoftDeleted('reservations', ['id' => 1]);
        $this->assertSame(1, Reservation::count());
    }

    public function test_real_bearer_token_authenticates_a_standard_user(): void
    {
        $user = User::findOrFail(1);
        $user->assignRole('standard');
        $token = Auth::guard('api')->login($user);
        Auth::forgetGuards();
        $this->withToken($token)->getJson('/api/v1/reservations')->assertOk();
    }

    public function test_invalid_bearer_token_is_rejected(): void
    {
        $this->withToken('invalid-token')->getJson('/api/v1/reservations')->assertUnauthorized();
    }

    public static function sidebarGrants(): array
    {
        return [
            'all actions' => [true, false],
            'direct read only' => [false, true],
            'no access' => [false, false],
        ];
    }

    #[DataProvider('sidebarGrants')]
    public function test_login_and_refresh_sidebar_and_permissions_follow_reservation_grants(bool $roleAccess, bool $directRead): void
    {
        $role = Role::findByName('standard', 'api');
        if (!$roleAccess) {
            $role->syncPermissions([]);
        }
        $user = User::findOrFail(1);
        $user->update(['password' => 'test-password', 'status' => 1]);
        $user->assignRole($role);
        if ($directRead) {
            $user->givePermissionTo('allReservation');
        }

        $login = $this->postJson('/api/v1/auth/login', [
            'userName' => 'operator', 'password' => 'test-password',
        ])->assertOk();
        Auth::forgetGuards();
        $refresh = $this->postJson('/api/v1/auth/refresh', [
            'refreshToken' => $login->json('refreshToken'),
        ])->assertOk();

        foreach ([$login, $refresh] as $response) {
            $sidebar = array_column($response->json('sidebar'), 'access', 'routeName');
            $this->assertTrue($sidebar['events']);
            $this->assertFalse($sidebar['users']);
            $this->assertSame($roleAccess || $directRead, $sidebar['reservations']);
            $this->assertCount(10, $response->json('sidebar'));
            $permissions = $response->json('permissions.reservations');
            $this->assertIsArray($permissions);
            $this->assertCount(5, $permissions);
            $permissions = array_column($permissions, 'access', 'permissionName');
            foreach (self::actions() as [$action]) {
                $this->assertArrayNotHasKey($action, $sidebar);
                $this->assertSame($roleAccess || ($directRead && $action === 'allReservation'), $permissions[$action]);
            }
        }
    }

    public function test_sql_is_repeatable_preserves_other_grants_and_respects_guards(): void
    {
        $standard = Role::findByName('standard', 'api');
        $unrelated = Permission::create(['name' => 'allTicket', 'guard_name' => 'api']);
        $standard->givePermissionTo($unrelated);
        $webRole = Role::create(['name' => 'standard', 'guard_name' => 'web']);
        Permission::create(['name' => 'allReservation', 'guard_name' => 'web']);
        $customRole = Role::create(['name' => 'customOperator', 'guard_name' => 'api']);
        $this->grantPermissions();
        $this->grantPermissions();
        $this->assertSame(7, Permission::count());
        $this->assertSame(31, DB::table('role_has_permissions')->count());
        $this->assertCount(5, $customRole->fresh()->permissions);
        $this->assertCount(0, $webRole->fresh()->permissions);
        $this->assertTrue($standard->fresh()->hasPermissionTo('allTicket'));
    }

    public function test_public_customer_booking_still_works_without_admin_authentication(): void
    {
        $this->postJson('/api/v1/client-reservations/create', $this->payload())->assertOk();
        $this->putJson('/api/v1/client-reservations/control', [
            'reservationId' => 1, 'token' => 'wrong-token', 'status' => 0,
        ])->assertUnprocessable();
        $this->assertSame(2, Reservation::count());
    }
}
