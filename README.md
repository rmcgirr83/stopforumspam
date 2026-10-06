# Stop Forum Spam

phpBB Stop Forum Spam extension (requires phpBB 3.3.19 or higher and PHP 7.4 or higher)

Extension will query the stop forum spam database on registration and posting (for guests only) and deny the post and or registration to go through if found. Will log an entry in the ACP if so set.

[![Build Status](https://github.com/phpbbmodders/stopforumspam/workflows/Tests/badge.svg)](https://github.com/phpbbmodders/stopforumspam/actions)

## Installation

### 1. clone
Clone (or download and move) the repository into the folder ext/phpbbmodders/stopforumspam:

```
cd phpBB3
git clone https://github.com/phpbbmodders/stopforumspam.git ext/phpbbmodders/stopforumspam/
```

### 2. activate
Go to admin panel -> tab customise -> Manage extensions -> enable Stop Forum Spam

Within the Admin panel visit the Extensions tab and within choose the settings for the extension.

## Upgrading from rmcgirr83/stopforumspam
This extension used to be installed as `rmcgirr83/stopforumspam`. Your settings, reported-post history and ACP module carry over:

1. If SFS Companion is installed, disable it first (see its README for the full order)
2. Go to your phpBB-Board > Admin Control Panel > Customise > Manage extensions > Stop Forum Spam: disable (do **not** delete its data)
3. Upload the new files to ext/phpbbmodders/stopforumspam
4. Go to your phpBB-Board > Admin Control Panel > Customise > Manage extensions > Stop Forum Spam: enable
5. Delete the ext/rmcgirr83/stopforumspam folder
6. Purge the board cache

If you use [SFS Companion](https://github.com/phpbbmodders/sfscompanion) or [Contact Admin](https://github.com/phpbbmodders/contactadmin), update them as well. SFS Companion needs the new name to enable, and the Contact Admin integration only works when both extensions use the phpbbmodders name.

## Update instructions:
1. Go to your phpBB-Board > Admin Control Panel > Customise > Manage extensions > Stop Forum Spam: disable
2. Delete all files of the extension from ext/phpbbmodders/stopforumspam
3. Upload all the new files to the same location
4. Go to your phpBB-Board > Admin Control Panel > Customise > Manage extensions > Stop Forum Spam: enable
5. Purge the board cache
