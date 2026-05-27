#!/usr/bin/env bash
# =============================================================================
# WpBlueprint installer
# Usage: bash <(curl -s https://raw.githubusercontent.com/SureshMayanglambam/WpBlueprint/main/install.sh)
# =============================================================================

set -e

# ── Colours ───────────────────────────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
BOLD='\033[1m'
RESET='\033[0m'

# ── Helpers ───────────────────────────────────────────────────────────────────
info()    { echo -e "${CYAN}  →${RESET} $1"; }
success() { echo -e "${GREEN}  ✔${RESET} $1"; }
warn()    { echo -e "${YELLOW}  ⚠${RESET} $1"; }
error()   { echo -e "${RED}  ✘${RESET} $1"; exit 1; }
ask()     { echo -e "${BOLD}  $1${RESET}"; }

# ── Banner ────────────────────────────────────────────────────────────────────
echo ""
echo -e "${CYAN}${BOLD}╔══════════════════════════════════════╗${RESET}"
echo -e "${CYAN}${BOLD}║       WpBlueprint  installer        ║${RESET}"
echo -e "${CYAN}${BOLD}╚══════════════════════════════════════╝${RESET}"
echo ""

# ── Dependency check ──────────────────────────────────────────────────────────
for cmd in git curl rsync; do
  command -v "$cmd" &>/dev/null || error "'$cmd' is required but not installed."
done

# ── Step 1 — WordPress themes directory ──────────────────────────────────────
echo -e "${BOLD}Step 1 of 3 — Locate your WordPress themes directory${RESET}"
echo ""
echo -e "  This theme must be installed inside your WordPress"
echo -e "  ${CYAN}wp-content/themes/${RESET} directory."
echo ""
ask "Path to your wp-content/themes/ directory:"
ask "(e.g. /var/www/html/wp-content/themes  or  ~/Sites/mysite/wp-content/themes)"
read -r THEMES_DIR
THEMES_DIR="${THEMES_DIR:-.}"

# Resolve absolute path
THEMES_DIR="$(cd "$(dirname "$THEMES_DIR/.")" && pwd)/$(basename "$THEMES_DIR")"

if [ ! -d "$THEMES_DIR" ]; then
  ask "Directory '$THEMES_DIR' does not exist. Create it? [Y/n]"
  read -r CREATE_DIR
  if [[ "$CREATE_DIR" =~ ^[Nn]$ ]]; then
    error "Installation cancelled."
  fi
  mkdir -p "$THEMES_DIR"
  success "Created '$THEMES_DIR'"
fi

# ── Theme folder name ─────────────────────────────────────────────────────────
echo ""
ask "Theme folder name inside themes/? (default: WpBlueprint)"
read -r THEME_SLUG
THEME_SLUG="${THEME_SLUG:-WpBlueprint}"

# Sanitise: lowercase, hyphens only
THEME_SLUG=$(echo "$THEME_SLUG" | tr '[:upper:]' '[:lower:]' | sed 's/[^a-z0-9-]/-/g')

THEME_DIR="$THEMES_DIR/$THEME_SLUG"

if [ -d "$THEME_DIR" ]; then
  warn "A theme folder '$THEME_SLUG' already exists."
  ask "Overwrite it? [y/N]"
  read -r OVERWRITE
  if [[ ! "$OVERWRITE" =~ ^[Yy]$ ]]; then
    error "Installation cancelled."
  fi
  rm -rf "$THEME_DIR"
fi

echo ""

# ── Step 2 — Download files ───────────────────────────────────────────────────
echo -e "${BOLD}Step 2 of 3 — Downloading theme files${RESET}"
echo ""

REPO_URL="https://github.com/SureshMayanglambam/WpBlueprint"
TMP_DIR="$(mktemp -d)"
trap 'rm -rf "$TMP_DIR"' EXIT

info "Cloning from ${REPO_URL} …"
git clone --quiet --depth 1 "$REPO_URL" "$TMP_DIR/repo" \
  || error "Could not clone repository. Check your internet connection."

# Copy theme files — exclude dev/build files not needed on the server
rsync -a \
  --exclude='.git' \
  --exclude='.DS_Store' \
  --exclude='install.sh' \
  --exclude='*.md' \
  --exclude='prepros.config' \
  --exclude='assets/scss' \
  --exclude='assets/video/output' \
  --exclude='node_modules' \
  "$TMP_DIR/repo/" \
  "$THEME_DIR/"

success "Theme copied to '$THEME_DIR'"
echo ""

# ── Step 3 — Theme namespace rename (optional) ────────────────────────────────
echo -e "${BOLD}Step 3 of 3 — Namespace & theme name${RESET}"
echo ""
echo -e "  By default the theme uses the namespace ${CYAN}WpBlueprint${RESET} and"
echo -e "  theme name ${CYAN}WP Blueprint${RESET}. You can rename them now, or"
echo -e "  skip and edit manually later."
echo ""
ask "Rename the theme? [y/N]"
read -r DO_RENAME

if [[ "$DO_RENAME" =~ ^[Yy]$ ]]; then

  ask "New theme name (shown in WP Admin, e.g. 'My Awesome Theme'):"
  read -r THEME_NAME
  THEME_NAME="${THEME_NAME:-WP Blueprint}"

  ask "New PHP namespace (no spaces, PascalCase, e.g. 'MyAwesomeTheme'):"
  read -r NAMESPACE
  NAMESPACE="${NAMESPACE:-WpBlueprint}"

  info "Renaming namespace to '${NAMESPACE}' and theme name to '${THEME_NAME}' …"

  # Replace namespace in all PHP files
  find "$THEME_DIR" -name "*.php" | while read -r file; do
    sed -i.bak \
      -e "s/namespace WpBlueprint/namespace ${NAMESPACE}/g" \
      -e "s/use WpBlueprint/use ${NAMESPACE}/g" \
      -e "s/'WpBlueprint/'${NAMESPACE}/g" \
      -e "s/WpBlueprint\\\\//${NAMESPACE}\\\\/g" \
      "$file"
    rm -f "${file}.bak"
  done

  # Update style.css theme header
  sed -i.bak "s/Theme Name: WP Blueprint/Theme Name: ${THEME_NAME}/" "$THEME_DIR/style.css"
  rm -f "$THEME_DIR/style.css.bak"

  success "Namespace and theme name updated"
fi

echo ""

# ── Done ──────────────────────────────────────────────────────────────────────
echo -e "${GREEN}${BOLD}╔══════════════════════════════════════╗${RESET}"
echo -e "${GREEN}${BOLD}║   Installation complete!  🎉         ║${RESET}"
echo -e "${GREEN}${BOLD}╚══════════════════════════════════════╝${RESET}"
echo ""
echo -e "  Theme installed at:"
echo -e "  ${CYAN}${BOLD}$THEME_DIR${RESET}"
echo ""
echo -e "  Next steps:"
echo -e "  ${BOLD}1.${RESET} Go to WP Admin → Appearance → Themes → activate ${CYAN}${THEME_SLUG}${RESET}"
echo -e "  ${BOLD}2.${RESET} Enable debug in wp-config.php to use the debug overlay:"
echo -e "     ${CYAN}define('WP_DEBUG', true);${RESET}"
echo -e "     ${CYAN}define('THEME_DEBUG', true);${RESET}"
echo -e "  ${BOLD}3.${RESET} Add your CPTs to ${CYAN}routes/web.php${RESET}"
echo -e "  ${BOLD}4.${RESET} Create models in ${CYAN}App/Models/${RESET} and controllers in ${CYAN}App/Controllers/${RESET}"
echo -e "  ${BOLD}5.${RESET} Add templates in ${CYAN}template/${RESET}"
echo ""
echo -e "  Docs: ${CYAN}https://github.com/SureshMayanglambam/WpBlueprint${RESET}"
echo ""