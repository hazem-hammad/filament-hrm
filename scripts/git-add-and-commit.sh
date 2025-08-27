#!/usr/bin/env bash
set -euo pipefail

# Navigate to repo root (script placed in repo/scripts)
cd "$(dirname "$0")/.." || exit 1

# Show status and prompt user to continue
git status --short
echo
read -rp "Proceed to git add -A and commit? [y/N] " confirm
if [[ "${confirm,,}" != "y" ]]; then
  echo "Aborted."
  exit 0
fi

# Stage all changes
git add -A

# Prompt for commit message (fallback to conventional default)
read -rp "Commit message (leave empty for default): " msg
if [[ -z "$msg" ]]; then
  msg="feat(jobs): add slug column and auto-generate slug on create/update"
fi

# Create commit
git commit -m "$msg"

# Show last commit summary
git --no-pager log -1 --stat --oneline
