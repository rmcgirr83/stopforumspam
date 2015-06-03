Stop Forum Spam
===============

phpBB 3.1 Stop Forum Spam extension

Extension will query the stop forum spam database on registration and posting (for guests only) and deny the post and or registration to go through if found. Will log an entry in the ACP if so set.

[![Build Status](https://travis-ci.org/RMcGirr83/phpBB-3.1-stopforumspam.svg?branch=master)](https://travis-ci.org/RMcGirr83/phpBB-3.1-stopforumspam)

## Installation

### 1. clone
Clone (or download and move) the repository into the folder ext/rmcgirr83/stopforumspam:

```
cd phpBB3
git clone https://github.com/RMcGirr83/phpBB-3.1-stopforumspam.git ext/rmcgirr83/stopforumspam/
```

### 2. activate
Go to admin panel -> tab customise -> Manage extensions -> enable Stop Forum Spam

Within the Admin panel visit the User Registration settings and within choose the settings for the extension.

## Update instructions:
1. Go to your phpBB-Board > Admin Control Panel > Customise > Manage extensions > Stop Forum Spam: disable
2. Delete all files of the extension from ext/rmcgirr83/stopforumspam
3. Upload all the new files to the same location
4. Go to your phpBB-Board > Admin Control Panel > Customise > Manage extensions > Stop Forum Spam: enable
5. Purge the board cache