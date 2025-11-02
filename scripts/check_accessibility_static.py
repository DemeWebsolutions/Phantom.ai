#!/usr/bin/env python3
import os, sys, json, re

root = sys.argv[1] if len(sys.argv) > 1 else "."
results = []

html_like = re.compile(r'\.(php|html|twig)$')
img_no_alt = re.compile(r'<img\b(?![^>]*\balt=)[^>]*>', re.IGNORECASE)
button_no_label = re.compile(r'<button\b(?![^>]*(aria-label|aria-labelledby|title))[^>]*>(\s*|<i[^>]*></i>)</button>', re.IGNORECASE)

for dirpath, _, filenames in os.walk(root):
    for fn in filenames:
        if not html_like.search(fn):
            continue
        path = os.path.join(dirpath, fn)
        try:
            with open(path, "r", encoding="utf-8", errors="ignore") as f:
                content = f.read()
            for m in img_no_alt.finditer(content):
                line = content[:m.start()].count("\n") + 1
                results.append({
                    "rule": "a11y-img-alt-missing",
                    "file": path,
                    "line": line,
                    "severity": "NOTICE",
                    "message": "Image tag missing alt attribute."
                })
            for m in button_no_label.finditer(content):
                line = content[:m.start()].count("\n") + 1
                results.append({
                    "rule": "a11y-button-label-missing",
                    "file": path,
                    "line": line,
                    "severity": "NOTICE",
                    "message": "Button may be missing accessible label (aria-label/title or visible text)."
                })
        except Exception:
            continue

print(json.dumps({"results": results}))