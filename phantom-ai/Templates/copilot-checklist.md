# Phantom.ai Copilot Submission Checklist Template
(Guaranteed First-Pass Copilot Comprehension)

## 1️⃣ Task Identification
- **Task ID:** `__________________________`
- **Task Name / Short Title:** `__________________________`
- **User Intent (Plain English):** `__________________________`
- **Project:** Phantom.ai (Standalone AI Interface / Workflow System)

## 2️⃣ Project Context (MANDATORY)
- Phantom.ai is an AI-orchestrated development system
- Copilot is used only for high-tier execution
- Phantom.ai handles:
  - Planning
  - Clarification
  - Review
  - Learning
- Output must be structured, deterministic, and reviewable
- This update builds on an existing project, not a greenfield rewrite

## 3️⃣ Task Type Classification (Escalation Control)
- ☐ Basic response → Cheap / Fast
- ☐ Review / analysis → Mid-tier
- ☐ Code generation → High-tier (Copilot)
- ☐ Testing / validation → Mid-tier

⚠ Copilot should only be invoked if Code generation is selected.

## 4️⃣ Design Assets (SVG — CRITICAL SECTION)

### Available Assets
- Phantom.ai SVG design files are provided
- SVG files are authoritative visual references

### SVG Files:
- `phantom-ai-01.svg`
- `phantom-ai-02.svg`

### Usage Instructions for Copilot
- Use the SVGs as-is (do NOT redraw, reinterpret, or simplify)
- Do NOT rasterize (no PNG/JPG conversion unless explicitly requested)
- Maintain original:
  - ViewBox
  - Aspect ratio
  - Paths
  - Gradients
  - Stroke widths
- SVGs may be:
  - Embedded inline
  - Referenced as static assets
  - Used in UI components
- Do NOT modify the SVG source unless explicitly instructed

### If Integration Is Required
- Wrap SVGs in semantic containers (div / component)
- Apply layout styles around, not inside, the SVG
- Do not inject inline styles into SVG paths unless specified

## 5️⃣ File Scope (Hard Boundary)
- **Files allowed to modify:** `__________________________`
- **Files NOT allowed to modify:** `__________________________`

⚠ Copilot must not touch files outside this list.

## 6️⃣ Constraints (Non-Negotiable)
- Follow WordPress / platform standards
- wp.org compliant
- No unrelated refactors
- Minimal prose — code first
- Comment assumptions only when unavoidable

## 7️⃣ Output Requirements
- Provide full code or diffs
- Clearly indicate where SVGs are used
- No speculative features
- Output must be parseable by Phantom.ai
- Ready for automated review & testing

## 8️⃣ Optional Metadata (For Phantom.ai Learning Loop)
- **Complexity level:** Low / Medium / High
- **Expected iteration count:** `___`
- **Known risks / edge cases:** `__________________________`
- **Reason Copilot escalation was required:** `__________________________`

## 9️⃣ Copilot Prompt (FINAL — Copy-Paste Only)

```
ROLE:
You are a developer contributing to the Phantom.ai project.

PROJECT CONTEXT:
- Phantom.ai orchestrates planning, review, and learning
- You are responsible ONLY for high-tier code execution
- SVG design assets are provided and must be used as-is

TASK:
<Insert exact task here>

DESIGN ASSETS:
- phantom-ai-01.svg
- phantom-ai-02.svg
Use these SVGs without modification unless explicitly instructed.

FILES TO MODIFY:
<Explicit list>

CONSTRAINTS:
- Modify only the listed files
- Do not alter SVG internals
- Maintain standards and compliance
- No unrelated refactors

OUTPUT:
- Provide code or diffs
- Clearly reference SVG usage
- Minimal prose
```

## 🔟 Pre-Submission Gate (Must Be YES)
- ☐ Is the project context fully explained?
- ☐ Are SVG usage rules explicit?
- ☐ Are file boundaries clear?
- ☐ Is Copilot being asked to execute, not decide?

❌ If any answer is NO → Do not submit to Copilot

## ✅ Final Phantom.ai Guarantee
If all checklist items are completed, Copilot:
- Has full project comprehension
- Will not misuse SVG assets
- Will not refactor unintended code
- Will deliver first-pass usable output
