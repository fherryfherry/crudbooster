# Changelog

## Unreleased

### CRUDBooster (Unreleased)
- **[NEW]** BlatUI Phase 0 - Migrated to Tailwind v4 (`@source`/`@custom-variant` in `app.css`, `tailwind.config.js` removed) and introduced the BlatUI JS engine, pre-bundled and registered onto Livewire's Alpine via `alpine:init`.

## v8.x

### CRUDBooster v8.3.0
- **[NEW]** Module Builder: pick a relation directly on the Database Table Schema step - auto-populates the Relationship join, a joined display column in Browse Grid, and a `select` field in Form Design.
- **[NEW]** Module Builder: re-saving the table schema now syncs new columns into existing Browse Grid / Form Design defaults without wiping prior customizations.
- **[NEW]** Module Builder: autosuggestion for the "Key" and "Label" fields in the Select "Option Configuration" modal.
- **[NEW]** Page Builder: inline Query Builder picker (modal) for query-based elements.
- **[NEW]** Dark mode support for API Builder and Audit Log modules.
- **[FIX]** SQLite: `SchemaUtil::getTableListing()` no longer returns schema-qualified table names (e.g. `main.table`), which broke relation joins.
- **[FIX]** Form: "Save & Add More" now shows the success alert like "Save" does.

### CRUDBooster v8.2.1
- **[FIX]** API Builder: fixed transparent overlay on the no-active-token modal.

### CRUDBooster v8.2.0
- **[NEW]** API Builder: auto-generate test token, polished Test API modal and edit form.

### CRUDBooster v8.1.0
- **[NEW]** Added Laravel 13 support.

### CRUDBooster v8.0.3
- **[FIX]** API Builder, Audit Log: fixed translation namespace collision and modal backdrop gap.

### CRUDBooster v8.0.2
- **[FIX]** Install: stopped hardcoding `APP_URL` to port 8000.

### CRUDBooster v8.0.1
- **[FIX]** Install: `cb:install` no longer ignores "No" answers during the interview.

### CRUDBooster v8.0.0
- **[BREAKING]** Public re-release: license system fully removed (`CbLicense`, `LicenseUtil`, `FeatureValidationService`, `PackageFeatureMiddleware`, `cb.feature` middleware, and the AI module-generation feature). `cb:install` is now purely interactive, no `--license`/`--reactivate` flags.

## v7.x

### CRUDBooster v7.9.12
- **[NEW]** File Upload Progress Bar - Added minimalist progress bar with real-time percentage for file and image uploads using Livewire events and Alpine.js
- **[NEW]** Summernote WYSIWYG Editor - Added Summernote rich text editor with image upload, auto-reformat, and CSS scoped styling to prevent conflicts with existing frameworks

### CRUDBooster v7.9.7
- **[ENH]** Enhanced filterSelectQuery with custom search logic - Added optional second parameter (searchClosure) for complex query customization
- **[FIX]** Form layout issue - Prevented empty columns from being rendered when all fields are hidden

### CRUDBooster v7.9.0
- **[ENH]** Advanced filter: Reset button next to Apply Filter.
- **[ENH]** Columns can disable filtering with `->filterable(false)`.
- **[FIX]** Date range filter no longer causes array to string conversion error.
- **[FIX]** Relation column filters (e.g., Product in Projects) must properly join and select relation fields.

### CRUDBooster v7.8.0
- **Added hookSearch feature** - Custom search logic for Livewire modules
- **Added advanced badgeable columns** - Multiple badge styles with custom colors
- **Enhanced Select transformLabel** - Closure support with model field access
- **UI/UX improvements** - Better grid table layout and responsiveness
- **Fixed showDetail** - Proper visibility of detail-only fields

### CRUDBooster v7.6.24
- **Fixed Select label transformation implementation**
  - Updated dataset format to use proper array structure instead of string format
  - Added transformation support to searchable select components
  - Fixed transformLabel() method to work correctly with both regular and searchable selects

### CRUDBooster v7.6.23
- **Added Select label transformation feature**
  - New transformLabel() method for customizing option labels
  - Supports both string and closure transformations
  - Works with regular and searchable select components

### CRUDBooster v7.6.22
- **Fixed grid table columns and sorting icons**
  - Added whitespace-nowrap class to prevent text wrapping
  - Improved table layout consistency

### CRUDBooster v7.6.21
- **Fixed additional action buttons in dropdown**
  - Resolved clickability issues in "threedot" dropdown menu

### CRUDBooster v7.6.20
- **Fixed file input validation**
  - Prevented file re-upload requirement when file already exists

### CRUDBooster v7

- Initial release of CRUDBooster v7
