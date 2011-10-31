<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<div id="about">
    <p>User experience is an important aspect of every web site and all web sites can benefit from effective caching and file size reduction. We have applied web site optimization methods typically used with high traffic sites and simplified their implementation. Coupling these methods either <a href="http://www.danga.com/memcached/" target="_blank">memcached</a> and/or opcode caching and the <acronym title="Content Delivery Network">CDN</acronym> of your choosing to provide the following features and benefits:</p>

    <ul>
		<li>Improved Google search engine ranking</li>
		<li>Increased visitor time on site</li>
		<li>Optimized progressive render (pages start rendering immediately)</li>
		<li>Reduced <acronym title="Hypertext Transfer Protocol">HTTP</acronym> Transactions, <acronym title="Domain Name System">DNS</acronym> lookups and reduced document load time</li>
		<li>Bandwidth savings via Minify and <acronym title="Hypertext Transfer Protocol">HTTP</acronym> compression of <acronym title="Hypertext Markup Language">HTML</acronym>, <acronym title="Cascading Style Sheet">CSS</acronym>, JavaScript and feeds</li>
		<li>Increased web server concurrency and increased scale (easily sustain high traffic spikes)</li>
		<li>Transparent content delivery network (<acronym title="Content Delivery Network">CDN</acronym>) integration with Media Library, theme files and WordPress core</li>
		<li>Caching of pages / posts in memory or on disk or on CDN (mirror only)</li>
		<li>Caching of (minified) <acronym title="Cascading Style Sheet">CSS</acronym> and JavaScript in memory, on disk or on <acronym title="Content Delivery Network">CDN</acronym></li>
		<li>Caching of database objects in memory or on disk</li>
		<li>Caching of objects in memory or on disk</li>
		<li>Caching of feeds (site, categories, tags, comments, search results) in memory or on disk</li>
		<li>Caching of search results pages (i.e. <acronym title="Uniform Resource Identifier">URI</acronym>s with query string variables) in memory or on disk</li>
		<li>Minification of posts / pages and feeds</li>
		<li>Minification (concatenation and white space removal) of inline, external or 3rd party JavaScript / <acronym title="Cascading Style Sheet">CSS</acronym> with automated updates</li>
		<li>Complete header management including <a href="http://en.wikipedia.org/wiki/HTTP_ETag">Etags</a></li>
		<li>JavaScript embedding group and location management</li>
		<li>Import post attachments directly into the Media Library (and <acronym title="Content Delivery Network">CDN</acronym>)</li>
    </ul>

    <p>Your users have less data to download, you can now serve more visitors at once without upgrading your hardware and you don't have to change how you do anything; just set it and forget it.</p>

    <h4>Who do I thank for all of this?</h4>

    <p>It's quite difficult to recall all of the innovators that have shared their thoughts, code and experiences in the blogosphere over the years, but here are some names to get you started:</p>

    <ul>
        <li><a href="http://stevesouders.com/" target="_blank">Steve Souders</a></li>
        <li><a href="http://mrclay.org/" target="_blank">Steve Clay</a></li>
        <li><a href="http://wonko.com/" target="_blank">Ryan Grove</a></li>
        <li><a href="http://www.nczonline.net/blog/2009/06/23/loading-javascript-without-blocking/" target="_blank">Nicholas Zakas</a> </li>
        <li><a href="http://rtdean.livejournal.com/" target="_blank">Ryan Dean</a></li>
        <li><a href="http://gravitonic.com/" target="_blank">Andrei Zmievski</a></li>
        <li>George Schlossnagle</li>
        <li>Daniel Cowgill</li>
        <li><a href="http://toys.lerdorf.com/" target="_blank">Rasmus Lerdorf</a></li>
        <li><a href="http://t3.dotgnu.info/" target="_blank">Gopal Vijayaraghavan</a></li>
        <li><a href="http://eaccelerator.net/" target="_blank">Bart Vanbraban</a></li>
        <li><a href="http://xcache.lighttpd.net/" target="_blank">mOo</a></li>
    </ul>

    <p>Please reach out to all of these people and support their projects if you're so inclined.</p>
</div>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>