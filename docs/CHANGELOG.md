Changelog
=========

0.1.3 (October 14, 2026) – Test release
--------------------------------------
- Fix: Allow verified Messenger 3.3.15 on HumHub 1.18, including the legacy administrator check, restoring the participant-management menu. Retain all previous versions and security checks.

0.1.2 (October 13, 2026) – Test release
--------------------------------------
- Enh: Allow Messenger 3.4.6 after functional verification on stable HumHub 1.19.0; retain Messenger 3.3.13 and 3.4.5 support.
- Test: Cover the expanded Messenger allowlist and run policy/version regression tests in the quality workflow.
- Fix: Return HTTP 503 for unsupported Messenger versions or a missing audit table in participant-management controllers and services using Yii's existing HttpException class.
- Test: Add availability regression checks and the stable HumHub 1.19.0 CI target while retaining existing 1.18 and beta targets.
