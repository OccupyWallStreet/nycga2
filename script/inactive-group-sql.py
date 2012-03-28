gids = map(int, "79,121,56,52,94,72,25,8,26,127,71,153,108,124,12,118,126,103,160,140,67,166,134,37,84,163,40,89,88,135,63,115,70,113,39,90,133,105,18,57,143,157,107,96,167,129,132".split(','))

print "DELETE FROM wp_bp_groups_groupmeta gm WHERE meta_key = 'active_status';"
print "INSERT INTO wp_bp_groups_groupmeta (group_id, meta_key, meta_value) VALUES (%d, 'active_status', 'inactive')" % gids[0],
for gid in gids[1:]:
    print ", (%d, 'active_status', 'inactive')" % gid,
print ";"

