# Art assets (Filament plugins directory)

Marketing images for [filamentphp.com/plugins](https://filamentphp.com/plugins).

Brand logos in `brand/` come from Transbank’s public [brand assets](https://transbankdevelopers.cl/brand-assets) (Webpay / Webpay Plus). Colors used in the layouts:

| Token | Hex | Use |
| --- | --- | --- |
| Purple | `#6D2077` | Primary (Webpay wordmark) |
| Magenta | `#D00070` | Accent / Plus |
| Cyan | `#009CDD` | Secondary accent |
| Gray | `#75787B` | Muted text |

| File | Size | Use |
| --- | --- | --- |
| `banner.jpg` | 2560 × 1440 | Main plugin image (required) |
| `thumbnail.jpg` | 1920 × 1080 | Optional tighter crop for the list |

Regenerate:

```bash
chrome="/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"

"$chrome" --headless=new --disable-gpu --hide-scrollbars \
  --force-device-scale-factor=2 --window-size=1280,720 \
  --screenshot=art/banner.png "file://$PWD/art/banner.html"

"$chrome" --headless=new --disable-gpu --hide-scrollbars \
  --force-device-scale-factor=1.5 --window-size=1280,720 \
  --screenshot=art/thumbnail.png "file://$PWD/art/thumbnail.html"

sips -s format jpeg -s formatOptions 90 art/banner.png --out art/banner.jpg
sips -s format jpeg -s formatOptions 90 art/thumbnail.png --out art/thumbnail.jpg
```

This folder is `export-ignore`d from the Composer dist archive.
