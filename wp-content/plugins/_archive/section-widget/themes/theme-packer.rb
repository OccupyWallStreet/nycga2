# Steps:
#  1. Download the latest STABLE theme pack here: http://code.google.com/p/jquery-ui/downloads/list
#  2. Drop the licenses into the /themes directory and get rid of everything else
#  3. Drop that package into the section-widget folder
#  4. Run this script from the /themes directory

# You'll need the yui-compressor gem
require 'rubygems'
require 'yui/compressor'

compressor = YUI::CssCompressor.new

# Get rid of the stuff we don't need
# Just because I'm lazy I'll let the OS do this for me (Sorry Windows folks)

system 'rm **/jquery-ui.css'
system 'rm **/ui.accordion.css'
system 'rm **/ui.all.css'
system 'rm **/ui.base.css'
system 'rm **/ui.datepicker.css'
system 'rm **/ui.dialog.css'
system 'rm **/ui.progressbar.css'
system 'rm **/ui.resizable.css'
system 'rm **/ui.slider.css'

Dir['./*'].each do |theme|
  if File.directory? theme
    # Save myself from dealing with exceptions
    system "rm #{theme}/sw-theme.dev.css"
    system "rm #{theme}/sw-theme.css"
    
    packed_css = ''
    
    # Pack the files in this order:
    # ui.core.css-> ui.theme.css-> ui.tabs.css
    ['ui.core.css','ui.theme.css','ui.tabs.css'].each do |filename|
      open(File.join(theme, filename)).each do |line|
        packed_css << '%scope% ' if line[0].chr == '.'
        packed_css << (line.gsub /, *?\./i, ', %scope% .')
      end
    end
    
    packed_css << "%scope% .ui-tabs .ui-tabs-nav>li:before{ content: '' !important; }" # Fix for WP's default theme
    
    open(File.join(theme, 'sw-theme.dev.css'), 'w') { |file| file.puts packed_css }
    
    min_css = packed_css.gsub /^\/\*.*?\*\//m, '' # Remove 'safe' comments in minified version
    
    open(File.join(theme, 'sw-theme.css'), 'w') { |file| file.puts compressor.compress(min_css) }
  end
end