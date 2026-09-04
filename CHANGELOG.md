# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.7.0] - 2026-09-04

Add bookmark image uploads and tag filtering, a responsive mobile sidebar, and
consistent GitHub title cleanup.

### Added

- **Bookmark image uploads.** Images can now be uploaded through both the web
  and API bookmark flows. Uploads are validated as image files and resized to
  512px, and the upload modal gains drag-and-drop, paste-to-upload, and a live
  preview. Covered by new feature and service tests.
- **Bookmark filtering by tags.** Bookmarks can be filtered by tag, with
  active-filter indicators and an empty-results message when a tag matches
  nothing. Tag and category management pages link into the filtered views.
- **Responsive mobile sidebar drawer.** The dashboard sidebar becomes a drawer
  on small screens with a menu toggle, backdrop dismissal, an explicit close
  control, Escape-key handling, and automatic closure after navigation.

### Changed

- **GitHub bookmark titles are cleaned consistently.** The noisy `"GitHub - "`
  prefix is stripped from titles fetched from `github.com` / `www.github.com`
  while titles from other hosts (and null titles) are left untouched. The
  shared cleanup logic in `BookmarkService` is now applied on both create and
  update, across the web and API flows.
- **More responsive home page bookmark icons.** The bookmark icon grid on the
  bookmarks index scales more gracefully across breakpoints.
- **Bookmarks toolbar adapts to small screens.** The toolbar wraps when space
  is tight, the "Sort by:" and "Per page:" labels are hidden on mobile, and the
  "Add Bookmark" button goes full-width when the controls stack.
- **Routes reference fully qualified controllers.** Controller `use` imports
  were removed from `routes/web.php` and `routes/api.php` in favour of fully
  qualified class names, Pint was configured to keep this consistent, and the
  convention was documented in the project guidance files (`AGENTS.md`,
  `GEMINI.md`, and the `.ai` / `.github` / `.junie` guideline files).

### Fixed

- **Search page edit/move modals were missing options during an active
  search.** The search page derived its "all categories" and "all tags" lists
  from the filtered search results, so edit and move modals lacked options
  whenever a search was active. `SearchController` now queries the user's
  complete category and tag lists and passes them as `allCategories` /
  `allTags` on both the empty-query and results responses.
- **GitHub title cleanup was skipped on bookmark updates.** Updating a
  bookmark now normalizes GitHub titles the same way creating one does, via the
  injected `BookmarkService`.

## [1.6.0] - 2026-07-12

- Replace flash banners with toast alerts.

[1.7.0]: https://github.com/aphoe/openbooklist/compare/1.6.0...1.7.0
[1.6.0]: https://github.com/aphoe/openbooklist/compare/1.5.0...1.6.0
