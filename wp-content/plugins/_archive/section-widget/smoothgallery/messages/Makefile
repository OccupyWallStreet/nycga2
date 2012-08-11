#
# Copied from:
# http://martin-grandrath.de/2009/04/wordpress-theme-internationalization-teil-2/
#

# List of locales to generate (space separated)
LOCALES = de_DE

# List of source files
SRC_FILES = ../*.php

# .pot file to create
POT_FILE = messages.pot

$(POT_FILE): $(SRC_FILES)
	xgettext --keyword=__ --keyword=_e --keyword=_c --keyword=__ngettext:1,2 --keyword=__ngettext_noop:1,2 $(SRC_FILES) && \
	mv messages.po $(POT_FILE)

po_files: $(patsubst %,%.po,$(LOCALES))

mo_files: $(patsubst %,%.mo,$(LOCALES))

%.po: $(POT_FILE)
	[ -f $@ ] && \
	msgmerge -U $@ $< && touch $@ || \
	msginit -l $* -o $@

%.mo: %.po
	msgfmt -o $@ $<
