#!/usr/bin/env python3
import os, sys, json, re

root = sys.argv[1] if len(sys.argv) > 1 else "."
results = []

# Readme checks
readme_path = None
for candidate in ["readme.txt", "README.txt"]:
    p = os.path.join(root, candidate)
    if os.path.isfile(p):
        readme_path = p
        break

if not readme_path:
    results.append({
        "rule": "readme-missing",
        "file": "",
        "line": 1,
        "severity": "WARNING",
        "message": "readme.txt not found at plugin root."
    })
else:
    with open(readme_path, "r", encoding="utf-8", errors="ignore") as f:
        txt = f.read()
    required_headers = ["Plugin Name:", "Stable tag:", "Requires at least:", "Tested up to:", "Requires PHP:"]
    for h in required_headers:
        if h not in txt:
            results.append({
                "rule": "readme-missing-header",
                "file": readme_path,
                "line": 1,
                "severity": "NOTICE",
                "message": f"Missing header: {h}"
            })

# i18n checks: very lightweight heuristic
php_files = []
for dirpath, _, filenames in os.walk(root):
    if any(part.startswith(".") for part in dirpath.split(os.sep)):
        continue
    for fn in filenames:
        if fn.endswith(".php"):
            php_files.append(os.path.join(dirpath, fn))

i18n_call_re = re.compile(r'\b(__|_e|_x|_n|esc_html__|esc_attr__|esc_html_e|esc_attr_e)\s*\(')

for pf in php_files:
    try:
        with open(pf, "r", encoding="utf-8", errors="ignore") as f:
            for idx, line in enumerate(f, start=1):
                if i18n_call_re.search(line):
                    # naive check: if only one argument inside call, likely missing text domain
                    if "(" in line and ")" in line and line.count(",") == 0:
                        results.append({
                            "rule": "i18n-missing-text-domain",
                            "file": pf,
                            "line": idx,
                            "severity": "WARNING",
                            "message": "Possible missing text domain in translation function."
                        })
                    # translators comment expectation when using placeholders (heuristic placeholder)
                    if "%s" in line or "%d" in line:
                        pass
    except Exception:
        continue

print(json.dumps({"results": results}))