#!/usr/bin/env bash
set -euo pipefail

PLUGIN_PATH="${1:-.}"
ARTIFACTS="./artifacts"
mkdir -p "$ARTIFACTS"

echo "🔍 Phantom: Deep WordPress Plugin Review"

# 0) Optional: PHP syntax lint (if available)
if command -v parallel-lint >/dev/null 2>&1; then
  echo "▶ PHP Parallel Lint"
  parallel-lint --colors "$PLUGIN_PATH" | tee "$ARTIFACTS/parallel-lint.txt" || true
fi

# 1) PHPCS + WPCS
echo "▶ PHPCS (WPCS)"
phpcs \
  --standard=WordPress,WordPress-Core,WordPress-Docs,WordPress-Extra \
  --extensions=php,css,js \
  --report=json \
  "$PLUGIN_PATH" > "$ARTIFACTS/phpcs.json" || true

# 2) PHPCompatibilityWP
echo "▶ PHPCompatibilityWP"
phpcs \
  --standard=PHPCompatibilityWP \
  --runtime-set testVersion 7.4-8.3 \
  --report=json \
  "$PLUGIN_PATH" > "$ARTIFACTS/phpcompat.json" || true

# 3) Readme + i18n (custom)
echo "▶ Readme + I18n checks"
python3 scripts/check_readme_i18n.py "$PLUGIN_PATH" > "$ARTIFACTS/readme_i18n.json" || echo '{}' > "$ARTIFACTS/readme_i18n.json"

# 4) Semgrep (WordPress rules)
echo "▶ Semgrep (WordPress security rules)"
if command -v semgrep >/dev/null 2>&1; then
  semgrep --config=scripts/semgrep/wordpress.yml --error --json --quiet \
    "$PLUGIN_PATH" > "$ARTIFACTS/semgrep.json" || true
else
  echo '{"results":[]}' > "$ARTIFACTS/semgrep.json"
fi

# 5) Accessibility (static)
echo "▶ Accessibility (static)"
python3 scripts/check_accessibility_static.py "$PLUGIN_PATH" > "$ARTIFACTS/a11y_static.json" || echo '{}' > "$ARTIFACTS/a11y_static.json"

# 6) Merge outputs to Phantom JSON
echo "▶ Merge to Phantom JSON"
if command -v jq >/dev/null 2>&1; then
  jq -s '{phpcs:.[0], phpcompat:.[1], readme_i18n:.[2], semgrep:.[3], a11y_static:.[4]}' \
    "$ARTIFACTS/phpcs.json" \
    "$ARTIFACTS/phpcompat.json" \
    "$ARTIFACTS/readme_i18n.json" \
    "$ARTIFACTS/semgrep.json" \
    "$ARTIFACTS/a11y_static.json" > "$ARTIFACTS/phantom-report.json"
else
  echo '{"phpcs":'$(cat "$ARTIFACTS/phpcs.json")","phpcompat":'$(cat "$ARTIFACTS/phpcompat.json")","readme_i18n":'$(cat "$ARTIFACTS/readme_i18n.json")","semgrep":'$(cat "$ARTIFACTS/semgrep.json")","a11y_static":'$(cat "$ARTIFACTS/a11y_static.json")'}' \
    > "$ARTIFACTS/phantom-report.json"
fi

# 7) Convert to SARIF
echo "▶ Convert to SARIF"
python3 scripts/to_sarif.py "$ARTIFACTS/phantom-report.json" > "$ARTIFACTS/phantom-report.sarif" || echo '{}' > "$ARTIFACTS/phantom-report.sarif"

# 8) Exit policy for CI (non-zero if any WARNING/ERROR)
if command -v jq >/dev/null 2>&1; then
  COUNT="$(jq -r '
    def msg_count(f):
      (f.files // {} | to_entries | map(.value.messages // []) | add
       | map(select(.type=="ERROR" or .type=="WARNING")) | length);
    def res_count(r):
      (r.results // [] | length);
    def cust_count(c):
      (c.results // [] | map(select(.severity=="ERROR" or .severity=="WARNING")) | length);
    { p: msg_count(.phpcs),
      c: msg_count(.phpcompat),
      s: res_count(.semgrep),
      r: cust_count(.readme_i18n),
      a: cust_count(.a11y_static) } as $t
    | ($t.p + $t.c + $t.s + $t.r + $t.a)
  ' "$ARTIFACTS/phantom-report.json" 2>/dev/null || echo 0)"
  echo "▶ Findings (WARN/ERROR) total: ${COUNT}"
  if [ "${COUNT:-0}" -gt 0 ]; then
    echo "❌ Phantom reported issues (>= WARNING)."
    exit 1
  fi
else
  echo "ℹ️ jq not available; not enforcing exit policy."
fi

echo "✅ Phantom complete. Artifacts in $ARTIFACTS"