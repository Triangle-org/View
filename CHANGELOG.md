# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

---

## [2.1.0] - 2026-11-21

### Added
- Support Triangle Engine 9 while retaining compatibility with Engine 8.
- Add an Engine-backed integration check for view configuration and template rendering.

### Fixed
- Include request-assigned variables in the default PHP renderer.
- Return and deduplicate post-render templates separately from pre-render templates.

