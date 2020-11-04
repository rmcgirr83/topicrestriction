Topic Restriction
=========================

phpBB extension which adds a new forum permission to restrict access to viewing topics to certain groups and forums.  Once enabled you must set which forums for which groups, under the group permissions of the forum, you want to restrict viewing of the topics.  Users will still be able to see the topic titles but not access the topic itself.


[![Build Status](https://travis-ci.com/rmcgirr83/topicrestriction.svg?branch=master)](https://travis-ci.com/rmcgirr83/topicrestriction)
## Installation

### 1. clone
Clone (or download and move) the repository into the folder ext/rmcgirr83/topicrestriction:

```
cd phpBB3
git clone https://github.com/rmcgirr83/topicrestriction.git ext/rmcgirr83/topicrestriction/
```

### 2. activate
Go to admin panel -> tab customise -> Manage extensions -> enable Topic Restriction

Within the Admin panel visit the Permissions tab -> Group Forum Permissions and choose which forums, under the Misc tab of the forum, for topics to be viewed or not for the group.  The permission will be called `Can view topics` and has to be set to No or Never to have the desired effect.
