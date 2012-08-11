print "Version Number? "
version = gets.chomp!

system "svn cp trunk tags/#{version}"
system "svn cp trunk tags/#{version}-lite"

system "svn ci -m 'Packed version #{version}'"

system "svn rm --force tags/#{version}/packer.rb"
system "svn rm --force tags/#{version}-lite/packer.rb"
system "svn rm --force tags/#{version}-lite/**/*.dev.*"
system "svn rm --force tags/#{version}-lite/screenshot*"

themes_to_remove = [
  'black-tie', 
  'blitzer', 
  'cupertino', 
  'dark-hive', 
  'dot-luv', 
  'eggplant', 
  'excite-bike', 
  'flick', 
  'hot-sneaks', 
  'humanity', 
  'le-frog', 
  'mint-choc', 
  'overcast', 
  'pepper-grinder', 
  'smoothness', 
  'south-street', 
  'start', 
  'sunny', 
  'swanky-purse', 
  'trontastic', 
  'ui-darkness', 
  'ui-lightness', 
  'vader'
]

themes_to_remove.each { |t| system "svn rm --force tags/#{version}-lite/themes/#{t}" }

system "svn ci -m 'Packed version #{version}-lite'"