#!/bin/bash
# Rebuild main.css. There is no build step in this project; this is a concatenation,
# run by hand whenever any source below changes.
#
#   token-bridge.css     prototype tokens -> theme.json presets
#   styles.css           the prototype's component CSS, verbatim (upstream source of truth)
#   theme-components.css components the prototype never had (initiative detail)
#   no-js.css            reveal-animation safety net
set -euo pipefail
HERE="$(cd "$(dirname "$0")" && pwd)"
PROTO="/Users/danscholz/projects/art4dev/art4dev-site/css/styles.css"
OUT="$HERE/../main.css"
cat "$HERE/token-bridge.css" "$PROTO" "$HERE/theme-components.css" "$HERE/no-js.css" > "$OUT"
echo "rebuilt $(basename "$OUT") ($(wc -l < "$OUT") lines)"
