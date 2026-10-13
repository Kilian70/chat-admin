---
name: humhub-development
description: Develop, review and maintain HumHub modules using version-matched core sources, official examples, security checks and documented tests. Default target for new projects is HumHub 1.19.
---

# HumHub Module Development

Use this skill when creating, updating, reviewing or migrating a HumHub module.

## Source of truth
This pilot adapts the upstream [skill](https://github.com/Kilian70/humhub-ai-dev/blob/3e9ca012767d0e249aab62f7ba5c48224e4e6b04/skills/humhub-development/SKILL.md) from branch `feature/humhub-119-skill`, pinned at commit `3e9ca012767d0e249aab62f7ba5c48224e4e6b04`.
Read the module's `AGENTS.md`, `README.md`, `module.json`, existing workflows and [pilot review](../../../docs/ai/SKILL-PILOT.md) before modifying code.
Read upstream [rules](https://github.com/Kilian70/humhub-ai-dev/blob/3e9ca012767d0e249aab62f7ba5c48224e4e6b04/docs/REGELN.md), [sources](https://github.com/Kilian70/humhub-ai-dev/blob/3e9ca012767d0e249aab62f7ba5c48224e4e6b04/docs/QUELLEN.md) and [version policy](https://github.com/Kilian70/humhub-ai-dev/blob/3e9ca012767d0e249aab62f7ba5c48224e4e6b04/docs/VERSIONEN.md). Resolve further upstream relative links against that same pinned repository, not this module. If essential sources are unavailable, report the gap before API-dependent changes. Preserve project-specific instructions.

## Version selection
- For **new** projects, propose **HumHub 1.19** as the default baseline.
- Determine the actual installed HumHub and PHP versions and supported target versions before coding. Never silently assume that an existing installation runs 1.19.
- Preserve HumHub 1.18 compatibility for existing modules when required; test each claimed version separately.
- For newer releases, follow the pinned [version policy](https://github.com/Kilian70/humhub-ai-dev/blob/3e9ca012767d0e249aab62f7ba5c48224e4e6b04/docs/VERSIONEN.md). Never infer compatibility from syntax checks alone.

## Workflow
1. Identify module repository, requested change, actual target versions, PHP versions and test environment. Ask for missing essential facts.
2. Read the relevant HumHub core at the matching release tag/commit, its AI guidance and migration notes, and at least one relevant official module. Record exact references.
3. Plan minimal changes using version-verified HumHub APIs, including permissions, CSRF, input validation, output escaping and migrations.
4. Implement only the requested scope. Preserve existing data and project-specific instructions.
5. Run available syntax, configuration and integration tests. Document what could not be tested.
6. Explain changed files, source versions, risks and manual checks for privileged and unprivileged users.
7. Wait for explicit approval before commit, push, merge, release or deployment.

## Tools and references
The upstream [module template](https://github.com/Kilian70/humhub-ai-dev/tree/3e9ca012767d0e249aab62f7ba5c48224e4e6b04/module-template), [tools](https://github.com/Kilian70/humhub-ai-dev/tree/3e9ca012767d0e249aab62f7ba5c48224e4e6b04/tools) and [setup instructions](https://github.com/Kilian70/humhub-ai-dev/blob/3e9ca012767d0e249aab62f7ba5c48224e4e6b04/START.md) belong to the framework, not this module. Compare before adopting them; do not install the whole template for a narrow change or overwrite existing workflows.

For this module:
- For stable 1.19 checks, the official [release notes](https://docs.humhub.org/docs/about/releasenotes/release_notes_1_19) and tag `v1.19.0` are available (remote ref SHA `8f22a72361841404b3b442306126650fc0f8153b`, verified 2026-10-13). Read the core at that ref and determine PHP/Messenger requirements before changing test targets. Include UserSource/account state, User Gates and attachment authorization with remote storage in functional checks. This pilot verified Messenger 3.4.6 on a fully installed local HumHub 1.19.0 instance and expanded the allowlist after passing functional HTTP tests. The final module passed again; prior versions remain supported. The previously missing Yii exception classes were corrected to HttpException(503); denial regression checks passed on 1.18.1, 1.18.5 and 1.19.0. Full-site checks included actual private attachment downloads, reinvitation, concurrent removals and impersonation. See the pilot review. The local installation was verified; production approval, PHP 8.2, S3/SSO and mobile browser testing remain separate checks.
- Keep `humhub.minVersion` at 1.18.1 unless a separately authorized compatibility change requires otherwise. Existing smoke targets are 1.18.5 / PHP 8.2, 1.19.0-beta.3 / PHP 8.3 and stable 1.19.0 / PHP 8.3; these are configured targets, not fresh test results. Do not label beta.3 as stable 1.19.
- Preserve the Messenger allowlist (3.3.13, 3.4.5 and 3.4.6), denial during impersonation, administrator membership checks, POST/CSRF protection and transactional removal/audit. Do not widen supported versions without source and runtime evidence.
- Run `php tests/run.php`, `php tests/access.php`, PHP lint and metadata checks when PHP CLI is available. Against each prepared core run `php tests/availability.php /path/to/humhub` for the controller/service denial regressions. The core smoke test requires a prepared HumHub checkout: `php tests/humhub-compatibility.php /path/to/humhub EXACT_VERSION`. It only loads the module class/configuration, not Messenger flows, HTTP authorization or the database.
- Follow the existing README's installation test checklist for functional validation, particularly direct attachment access, impersonation and concurrent removals. Actual installed HumHub/PHP versions and the test environment are unknown until supplied or inspected.

This skill is an instruction entry point, not evidence that a module is compatible or secure.
