from pathlib import Path
from PIL import Image, ImageDraw

folder = Path(r"C:\xampp\htdocs\urbania-backend-api\tmp\pdfs")
pages = sorted(
    (path for path in folder.glob("urbania-guide-*.png") if path.stem.rsplit('-', 1)[-1].isdigit()),
    key=lambda path: int(path.stem.rsplit('-', 1)[-1]),
)
thumb_w = 380
gap = 24
label_h = 30
thumbs = []
for page in pages:
    image = Image.open(page).convert("RGB")
    height = round(image.height * thumb_w / image.width)
    thumbs.append((page.stem.rsplit('-', 1)[-1], image.resize((thumb_w, height))))

rows = (len(thumbs) + 1) // 2
row_h = max(im.height for _, im in thumbs) + label_h
sheet = Image.new("RGB", (thumb_w * 2 + gap * 3, rows * row_h + gap * (rows + 1)), "#e7edf5")
draw = ImageDraw.Draw(sheet)
for index, (label, image) in enumerate(thumbs):
    col, row = index % 2, index // 2
    x = gap + col * (thumb_w + gap)
    y = gap + row * (row_h + gap)
    sheet.paste(image, (x, y + label_h))
    draw.text((x, y + 7), f"Page {int(label)}", fill="#10223e")

sheet.save(folder / "urbania-guide-contact-sheet.png")
