# Art assets (Filament plugins directory)

Marketing images for [filamentphp.com/plugins](https://filamentphp.com/plugins).

| File | Size | Use |
| --- | --- | --- |
| `banner.jpg` | 2560 × 1440 | Main plugin image (required) |
| `thumbnail.jpg` | 1920 × 1080 | Optional tighter crop for the list |

Regenerate from HTML (Chrome headless):

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
