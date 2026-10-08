# TODO

Ideas not yet built, practical and speculative alike.

## Disapprove and report in one step

Requested on phpBB.com:
https://www.phpbb.com/customise/db/extension/phpbb_3.1_stop_forum_spam/support/topic/250564

Add a "Report to Stop Forum Spam" checkbox to the screen moderators see when
they disapprove a post from the moderation queue, so a spam post can be
disapproved and its poster reported in one step. Today the report button
only appears on posts shown in a topic or private message.

Planned behavior:

- The checkbox shows only when disapproving (not approving), only to staff
  allowed to report, and only when the extension is enabled with an API key.
- On submit, `core.disapprove_posts_after` reports each disapproved post's
  poster the same way the existing report button does, skipping posters
  already reported, guests, and administrators or moderators.
- Unchecked by default.

Blocked on phpBB: `mcp_approve.html`, the template for that screen, has no
template events in phpBB 3.3 or `master`, so an extension can't add the
checkbox. The PHP side already works: the confirm form, including its AJAX
pop-up, posts every field back. The event request is written up in
[`docs/events/mcp_approve_reason_after.txt`](events/mcp_approve_reason_after.txt).

Still needs deciding:

- Whether to also offer a way to warn the user, as the requester asked.
  phpBB's warnings are a separate MCP tool, so this may be better left to
  that tool.
- Whether to add a "Report to Stop Forum Spam" link on the MCP post details
  page (`mcp_post_report_buttons_top_after` already exists there) as a way
  to report unapproved posts before the template event lands.

Reporting users who never posted, asked for in the same topic, is already
possible with phpbbmodders/sfscompanion (*Scan users*, and the *Check SFS*
link on profiles) and phpbbmodders/ban-hammer (report from the profile).
