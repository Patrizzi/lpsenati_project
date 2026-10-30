---
name: TechStock
description: Pliego de Registro Editorial — Sistema de Gestión de Inventarios Tecnológicos
colors:
  ground: "#F5F4F6"
  ground-warm: "#FAF6F5"
  ground-cool: "#EEF3EF"
  sheet: "#FAF9FA"
  surface: "#FFFFFF"
  ink: "#16181A"
  ink-muted: "#4A4E54"
  ink-subtle: "#686D73"
  hairline: "#C9C7C4"
  seal: "#DCDDF3"
  alert-critical-ink: "#B91C1C"
  alert-critical-bg: "#FEF2F2"
  alert-critical-border: "#FCA5A5"
  alert-warning-ink: "#B45309"
  alert-warning-bg: "#FFFBEB"
  alert-warning-border: "#FCD34D"
  alert-verified-ink: "#15803D"
  alert-verified-bg: "#F0FDF4"
  alert-verified-border: "#86EFAC"
  dark-ground: "#121316"
  dark-ground-warm: "#17181C"
  dark-ground-cool: "#0E1013"
  dark-sheet: "#181A1E"
  dark-surface: "#1E2025"
  dark-subtle-surface: "#262930"
  dark-hover-sheet: "#23262D"
  dark-ink: "#F0EFEA"
  dark-ink-muted: "#B4B7BE"
  dark-ink-subtle: "#8E929B"
  dark-ink-hairline: "#5C6069"
  dark-hairline: "#2E3239"
  dark-seal: "#282B42"
  dark-selection: "#383C5C"
  dark-alert-crit-bg: "#2C1215"
  dark-alert-crit-border: "#7F1D1D"
  dark-alert-crit-ink: "#FCA5A5"
  dark-alert-crit-hover: "#DC2626"
  dark-alert-warn-bg: "#291D0A"
  dark-alert-warn-border: "#78350F"
  dark-alert-warn-ink: "#FCD34D"
  dark-alert-ok-bg: "#0D2618"
  dark-alert-ok-border: "#14532D"
  dark-alert-ok-ink: "#86EFAC"
typography:
  display:
    fontFamily: "'Schibsted Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
    fontSize: "2.25rem"
    fontWeight: 400
    lineHeight: 1.2
    letterSpacing: "-0.02em"
  headline:
    fontFamily: "'Schibsted Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
    fontSize: "1.125rem"
    fontWeight: 600
    lineHeight: 1.3
  title:
    fontFamily: "'Schibsted Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
    fontSize: "1rem"
    fontWeight: 600
    lineHeight: 1.4
  body:
    fontFamily: "'Schibsted Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 400
    lineHeight: 1.5
  dense:
    fontFamily: "'Schibsted Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
    fontSize: "12px"
    fontWeight: 400
    lineHeight: 1.4
  dense-alt:
    fontFamily: "'Schibsted Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
    fontSize: "13px"
    fontWeight: 400
    lineHeight: 1.4
  label:
    fontFamily: "'DM Mono', monospace"
    fontSize: "11px"
    fontWeight: 500
    lineHeight: 1
    letterSpacing: "0.14em"
  caption:
    fontFamily: "'DM Mono', monospace"
    fontSize: "10px"
    fontWeight: 500
    lineHeight: 1.2
    letterSpacing: "0.08em"
  tag:
    fontFamily: "'DM Mono', monospace"
    fontSize: "9px"
    fontWeight: 600
    lineHeight: 1
  micro:
    fontFamily: "'DM Mono', monospace"
    fontSize: "8px"
    fontWeight: 600
    lineHeight: 1
rounded:
  none: "0px"
  sm: "2px"
  full: "9999px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"
  token-bar: "64px"
  nav-rail: "240px"
components:
  button-primary:
    backgroundColor: "{colors.ink}"
    textColor: "{colors.ground}"
    rounded: "{rounded.none}"
    padding: "10px 16px"
  button-primary-hover:
    backgroundColor: "{colors.ink-muted}"
  button-secondary:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.ink}"
    rounded: "{rounded.none}"
    padding: "10px 16px"
  badge-critical:
    backgroundColor: "{colors.alert-critical-bg}"
    textColor: "{colors.alert-critical-ink}"
    rounded: "{rounded.none}"
    padding: "2px 8px"
  badge-warning:
    backgroundColor: "{colors.alert-warning-bg}"
    textColor: "{colors.alert-warning-ink}"
    rounded: "{rounded.none}"
    padding: "2px 8px"
  badge-verified:
    backgroundColor: "{colors.alert-verified-bg}"
    textColor: "{colors.alert-verified-ink}"
    rounded: "{rounded.none}"
    padding: "2px 8px"
---

# Design System: TechStock

## Overview

**Creative North Star: "Pliego de Registro Editorial"**

TechStock operates as a high-precision Swiss editorial plate register printed on uncoated paper stock. It refuses the generic dark-slate and neon SaaS dashboard rut in favor of exposed 1px hairlines, corner registration crosshairs (`+`), deep carbon ink, and absolute typographic fidelity. The system treats inventory records not as ephemeral cards, but as permanent, audited ledger sheets with physical presence and undeniable clarity.

Every inventory figure, SKU identifier, and stock movement is presented with documentary authority. The atmosphere subtly shifts across the viewport from warm uncoated paper (`#FAF6F5`) in the top-left to cool register tint (`#EEF3EF`) in the bottom-right, framing crisp elevated sheet cells (`#FAF9FA` and `#FFFFFF`). Minimum stock thresholds (`stock <= stock_minimo`) surface instantly through disciplined semantic ink without noisy gradients or decorative halos.

**Key Characteristics:**
- Uncoated editorial ground with subtle atmospheric drift and exposed structural hairlines (`1px solid #C9C7C4`).
- Deep carbon typography in neo-grotesque `Schibsted Grotesk` paired with documentary `DM Mono`.
- Sharp 0px corner geometry across buttons, cards, tables, inputs, and state pills.
- Signature letterpressed periwinkle circular verification seal (`88x88` SVG with rotating `textPath`).
- Strict tabular numeral alignment (`tabular-nums`) across all SKUs, stock counts, and currency amounts.

## Colors

The palette reproduces the tactile quality of an industrial inventory register: carbon ink stamped upon heavy uncoated paper, punctuated by periwinkle editorial audit stamps and strictly semantic state alerts. It operates across two dedicated, authentic materiality states: **Pliego de Registro Editorial** (Light Mode) and **Pliego de Carbón No Estucado** (Dark Mode).

### Light Mode (Normative Default)
- **Primary Ink (Deep Carbon):** `#16181A` (body, headers, primary buttons, active rail).
- **Secondary Ink (Muted / Subtle):** `#4A4E54` and `#686D73` (descriptions, table headers, timestamps).
- **Ground (Uncoated Paper):** `#F5F4F6` with subtle atmospheric drift (`#FAF6F5` warm top-left to `#EEF3EF` cool bottom-right).
- **Elevated Sheet & Surface:** `#FAF9FA` and `#FFFFFF`.
- **Structural Hairlines:** `#C9C7C4` (1px perimeter rules, division lines, corner crosshairs `+`).
- **Verification Seal:** `#DCDDF3` with `#16181A` ink.

### Dark Mode: "Pliego de Carbón No Estucado" (`html.dark`)
- **Primary Ink (Warm Chalk):** `#F0EFEA` (high-contrast documentary chalk ink, never harsh pure `#FFFFFF`).
- **Secondary Ink (Muted / Subtle):** `#B4B7BE` ($\ge 7:1$ contrast) and `#8E929B` ($\ge 4.6:1$ contrast).
- **Ground (Uncoated Carbon Stock):** `#121316` with atmospheric drift (`#17181C` top-left to `#0E1013` bottom-right).
- **Elevated Sheet & Surface:** `#181A1E` (sheet containers) and `#1E2025` (elevated white cells and inputs).
- **Structural Hairlines:** `#2E3239` (crisp 1px division rules).
- **Verification Seal:** `#282B42` (deep indigo-periwinkle stamp fill with `#F0EFEA` chalk ink and stroke).

### Semantic State Colors
- **Critical Alert / Salida / Out of Stock:**
  - Light: `#B91C1C` ink on `#FEF2F2` fill with `#FCA5A5` 1px border.
  - Dark: `#FCA5A5` ink on `#2C1215` fill with `#7F1D1D` 1px border.
- **Warning Threshold / Near Minimum:**
  - Light: `#B45309` ink on `#FFFBEB` fill with `#FCD34D` 1px border.
  - Dark: `#FCD34D` ink on `#291D0A` fill with `#78350F` 1px border.
- **Verified / Entrada / In Stock:**
  - Light: `#15803D` ink on `#F0FDF4` fill with `#86EFAC` 1px border.
  - Dark: `#86EFAC` ink on `#0D2618` fill with `#14532D` 1px border.

### Named Rules
**The Ink and Paper Rule.** No surface in TechStock uses pure black (`#000000`) or cold digital blue-gray. Ink is deep carbon (`#16181A`) in light mode and warm chalk (`#F0EFEA`) in dark mode, preserving the documentary tactile character of a physical ledger.

**The Semantic Reserve Rule.** Vibrant colors are banned from decorative ornamentation. Red, amber, and green exist solely to convey inventory availability, stock deficits, and movement transactions.

**The Dual Paper Register Rule.** The dark theme is not a synthetic dark-slate skin, but an authentic carbon-paper plate register ("Pliego de Carbón"). Primary buttons invert cleanly between solid carbon ink on paper (`#16181A`) and solid chalk ink on carbon (`#F0EFEA`), maintaining instant visual hierarchy.

## Typography

**Display Font:** `Schibsted Grotesk` (with `-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif` fallback)  
**Body Font:** `Schibsted Grotesk` (with standard system grotesque fallbacks)  
**Label/Mono Font:** `DM Mono` (with `ui-monospace, SFMono-Regular, monospace` fallback)

**Character:** A high-contrast pairing between an authentic Nordic neo-grotesque editorial face with open apertures and a clean fixed-pitch monospace face that lends documentary credibility to every measurement.

### Hierarchy
- **Display** (weight `400`, size `2.25rem` / `36px`, line-height `1.2`, letter-spacing `-0.02em`): Used for primary screen headings (`Inventario en tiempo real`, `Productos y existencias`).
- **Headline** (weight `600`, size `1.125rem` / `18px`, line-height `1.3`): Used for major card headers, section titles, and modal dialog titles.
- **Title** (weight `600`, size `1rem` / `16px`, line-height `1.4`): Used for table grouping headers and sub-panel sections.
- **Body** (weight `400`, size `0.875rem` / `14px`, line-height `1.5`, measure `65–75ch`): Used for operational guidance, instructions, product names, and flash notifications.
- **Dense Text** (weight `400`, size `0.75rem` / `12px` / `13px`, line-height `1.4`): Used for dense tables, forms, and instructions.
- **Label** (weight `500`, size `0.6875rem` / `11px`, letter-spacing `0.14em`, uppercase): Used for table column headers, status badges, coordinate stamps, and button typography.
- **Caption** (weight `500`, size `0.625rem` / `10px`, letter-spacing `0.08em`, uppercase): Used for secondary token tags, SKU badges, and sub-labels.
- **Micro** (weight `600`, size `0.5rem` / `8px` / `9px`, line-height `1`): Used for circular seal unit labels and operator tags.

### Named Rules
**The No-Eyebrow Rule.** A kicker or eyebrow above a heading is strictly banned. Headings speak for themselves; contextual module coordinates belong in the sticky top token bar, to the right, or beneath the heading as a secondary description.

**The Tabular Precision Rule.** All numeric columns, currency amounts in Soles (`S/`), inventory quantities, and SKU codes must specify `tabular-nums` (`font-variant-numeric: tabular-nums`) so numbers stack in perfect vertical columns.

## Layout

The operational shell is anchored by a persistent two-axis frame:
- **Sticky 64px Top Token Bar** (`h-16`, `border-b border-[#C9C7C4]`): Provides constant operator coordinates, live low-stock count pill with active pulse, synchronization state, and operator monogram profile link.
- **240px Left Navigation Rail** (`w-60`, `border-r border-[#C9C7C4]`, `bg-[#FAF9FA]`): Hosts the 8 primary operational modules with distinct active fills (`#16181A` with `#F5F4F6` text) and sticky bottom session closure.
- **Main Working Sheet** (`max-w-7xl mx-auto`, padding `24px` to `40px`): Elevated content plates registration-marked with corner crosshairs `+`.
- **Spacing Rhythm:** Based on an 8px grid (`8px`, `16px`, `24px`, `32px`, `48px`). Tight grouping within form fields, generous spacing between discrete analytical plates.

## Elevation & Depth

TechStock refuses artificial zero-offset halos and dramatic digital drop shadows. Depth is conveyed exclusively through tonal layering, contrasting paper materials, and razor-sharp hairlines.

### Shadow Vocabulary
- **Zero-Shadow Default:** All cards, plates, and tables rest flat with `box-shadow: none` and `border: 1px solid #C9C7C4`.
- **Subtle Ambient Backdrop Blur:** Sticky top bar uses `backdrop-blur-sm` over the scrolling paper ground to signal operational layering without drop shadow decoration.

### Named Rules
**The Flat-By-Default Rule.** Hard offset shadows (`4px 4px 0`) and heavy colored glows are prohibited. Structural separation is achieved via `1px solid #C9C7C4` hairlines and shifts between `#FAF9FA` sheet plates and `#FFFFFF` data cells.

## Shapes

The form language is unapologetically rectangular, precise, and uncompromising:
- **Sharp Corners (0px radius):** Buttons, inputs, tables, cards, badges, and alerts are sharp rectangles (`rounded: 0px`).
- **Registration Crosshairs (`+`):** `12x12` SVG marks placed at the exterior corners of major plates to simulate physical printing registration.
- **The Circular Stamp Exception:** The only rounded geometries in the entire system are circular indicator dots (6px) and the signature `88x88` rotating verification seal.

## Components

### Buttons
- **Shape:** Sharp rectangle (`border-radius: 0px`).
- **Primary:** `#16181A` background with `#F5F4F6` uppercase monospace text (`11px`, `tracking-[0.14em]`, padding `10px 16px`).
- **Hover / Focus:** Shifts to `#4A4E54` on hover. Focus triggers crisp `1.5px solid #16181A` outline with `2px` offset.
- **Secondary:** Transparent or `#FFFFFF` background with `1px solid #16181A` border and `#16181A` text. Inverts to `#16181A` background on hover.

### State Chips / Badges
- **Shape:** Sharp rectangle (`0px` radius, padding `2px 8px`).
- **Typography:** `10px` or `11px` monospace uppercase (`DM Mono`, bold).
- **Critical / Stock Bajo:** `#FEF2F2` background, `#FCA5A5` 1px border, `#B91C1C` text.
- **Warning / En Umbral:** `#FFFBEB` background, `#FCD34D` 1px border, `#B45309` text.
- **Available / Disponible:** `#F0FDF4` background, `#86EFAC` 1px border, `#15803D` text.

### Cards / Analytical Plates
- **Corner Style:** Sharp 0px radius, registered with exterior 12x12 SVG crosshairs.
- **Background:** `#FAF9FA` with `#FFFFFF` sub-tables.
- **Border:** `1px solid #C9C7C4`.
- **Internal Padding:** `24px` to `32px`.

### Inputs / Form Fields
- **Style:** `#FFFFFF` background, `1px solid #C9C7C4` border, 0px radius, padding `8px 12px`.
- **Focus:** Border transitions to `#16181A`, outline ring `1.5px solid #16181A` with `2px` offset.
- **Caret:** Explicit `#16181A` carbon ink.

### Signature Verification Stamp
- **SVG Circular Seal:** `88x88` circular seal with `#DCDDF3` fill, `#16181A` stroke, and concentric dashed inner ring.
- **Rotating textPath:** Text reading `TECHSTOCK · AUDITORÍA DE EXISTENCIAS · VERIFICADO ·` rotating with `25s linear infinite` animation (pauses when `prefers-reduced-motion` is active). Center displays live physical unit count.

## Do's and Don'ts

### Do:
- **Do** format all numerical amounts, SKU codes, stock figures, and dates with `tabular-nums` and monospace styling.
- **Do** frame analytical plates and major headers with 12x12 SVG registration crosshairs (`+`) at the corners.
- **Do** style browser surfaces: `::selection` in `#DCDDF3`, custom `#C9C7C4` scrollbars, and explicit `#16181A` focus rings.
- **Do** compute low stock strictly with the determinist rule: `stock <= stock_minimo`.
- **Do** use crisp inline SVGs with consistent `1.5px` or `1.25px` stroke for all icons.

### Don't:
- **Don't** ever place a kicker or eyebrow label directly above an `<h1>`, `<h2>`, or `<h3>` heading.
- **Don't** use colored `border-left` or `border-right` exceeding 1px on cards, table rows, or alert banners.
- **Don't** apply hard offset block drop shadows (`box-shadow: 4px 4px 0`) or glowing zero-offset halos.
- **Don't** use emojis or Unicode symbols as icons; draw authored, stroke-consistent inline SVGs.
- **Don't** use generic dark slate, pure black (`#000000`), or neon accents that break the uncoated editorial register aesthetic.
