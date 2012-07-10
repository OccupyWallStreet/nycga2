<?php
/**
 * Page Shortcodes WordPress Plugin
 */

/**
 * Page Shortcodes WordPress Plugin
 */
class PageShortcodesPlugin {
    
    /**
     * Our instance.
     * @var PageShortcodesPlugin
     */
    static private $INSTANCE = null;
    
    /**
     * Cache pages by ID.
     * @var array
     */
    static private $CACHE_BY_ID = array();

    /**
     * Cache pages by ID.
     * @var array
     */
    static private $CACHE_BY_NAME = array();
    
    /**
     * Plugin directory.
     * @var unknown_type
     */
    private $pluginDir = null;

    /**
     * Constructor.
     */
    private function __construct($pluginFile) {
        $this->pluginDir = dirname($pluginFile);
        add_action('init', array($this, 'handleWpAction_init'));
    }
    
    /**
     * Cache a page
     * @param std_object $page
     */
    protected function cache($page = null) {
        if ( $page ) {
            self::$CACHE_BY_ID[$page->ID] = $page;
            self::$CACHE_BY_NAME[$page->post_name] = $page;
        }
    }

    /**
     * Find a page
     * @param array $atts Shortcode attributes
     * @param mixed $content Fallback content
     */
    protected function findPage($atts, $content = null) {
        extract(shortcode_atts(array(
            'id' => null,
            'name' => null,
        ), $atts));
        $foundPage = null;
        if ( $id ) {
            if ( isset(self::$CACHE_BY_ID[$id]) ) return $CACHE_BY_ID[$id];
            $theQuery = new WP_Query(array(
                'post_type' => 'page',
                'page_id' => $id,
            ));
            if ( count($theQuery->posts) ) {
                $foundPage = $theQuery->posts[0];
            }
        }
        elseif ( $name ) {
            if ( isset(self::$CACHE_BY_NAME[$name]) ) return self::$CACHE_BY_NAME[$name];
            $foundPage = get_page_by_path($name);
        }
        if ( $foundPage ) $this->cache($foundPage);
        return $foundPage;
    }
    
    /**
     * Handle the WordPress 'page_title' shortcode.
     */    
    function handleWpShortcode_page_title($atts, $content = null) {
        $page = $this->findPage($atts, $content);
        if ( $page ) return $page->post_title;
        return $content;
    }
    
    /**
     * Handle the WordPress 'page_content' shortcode.
     */    
    function handleWpShortcode_page_content($atts, $content = null) {
        $page = $this->findPage($atts, $content);
        if ( $page ) return $page->post_content;
        return $content;
    }
    
    /**
     * Handle the WordPress 'page_permalink' shortcode.
     */    
    function handleWpShortcode_page_permalink($atts, $content = null) {
        $page = $this->findPage($atts, $content);
        if ( $page ) return get_permalink($page->ID);
        return $content;
    }
    
    /**
     * Handle the WordPress 'page_meta' shortcode.
     */    
    function handleWpShortcode_page_meta($atts, $content = null) {
        extract(shortcode_atts(array(
            'id' => null,
            'name' => null,
            'meta' => null,
            'template' => null,
        ), $atts));
        $page = $this->findPage(array('id' => $id, 'name' => $name), $content);
        if ( $page ) {
            $data = get_post_meta($page->ID,$meta);
            $templates = array();
            if ( $template ) {
                $templates = $this->createTemplateSearchPath($page, $template, $templates);
            }
            $templates = $this->createTemplateSearchPath($page, 'psp-page-meta', $templates);
            $foundTemplate = locate_template($templates);
            if ( ! $foundTemplate ) {
                $foundTemplate = $this->localTemplate('psp-page-meta.php');
            }
            return $this->includeTemplate($foundTemplate, array(
                'pspPageMeta' => isset($data[0]) ? $data[0] : null,
            ));
        }
        return $content;
    }
    
    /**
     * Handle the WordPress 'page_list' shortcode.
     */    
    function handleWpShortcode_page_list($atts, $content = null) {
        extract(shortcode_atts(array(
            'id' => null,
            'name' => null,
            'template' => null,
        ), $atts));
        if ( $name ) {
            $page = $this->findPage(array('name' => $name));
            if ( $page ) $id = $page->ID;
        }
        if ( ! $id ) return $content;
        $queryArgs = array('post_type' => 'page', 'post_parent' => $id, 'orderby' => 'title', 'order' => 'ASC', );
        $theQuery = new WP_Query($queryArgs);
        $posts = $theQuery->posts;
        if ( count($posts) ) {
            foreach ( $posts as $foundPage ) $this->cache($foundPage);
        }
        $output = '';

        $templates = array();

        if ( $template ) {
            $templates = $this->createTemplateSearchPath($page, $template, $templates);
        }
        $templates = $this->createTemplateSearchPath($page, 'psp-page-list', $templates);
        $foundTemplate = locate_template($templates);
        if ( ! $foundTemplate ) {
            $foundTemplate = $this->localTemplate('psp-page-list.php');
        }
        
        return $this->includeTemplate($foundTemplate, array(
            'pspPageList' => $posts,
        ));
        
    }
    
    protected function createTemplateSearchPath($page, $templateName, $templates = null) {
        if ( $templates === null ) $templates = array();
        $templates[] = implode('-', array('page', $page->post_name, $templateName . '.php'));
        $templates[] = implode('-', array('page', $page->ID, $templateName . '.php'));
        $templates[] = implode('-', array('page', $templateName . '.php'));
        $templates[] = $templateName . '.php';
        return $templates;
    }
    
    protected function localTemplate($template) {
        return implode('/', array($this->pluginDir, 'templates', $template));
    }
    
    /**
     * Include a template.
     * @param string $template
     * @param array $data
     */
    protected function includeTemplate($template, $data = null){
        if ( $data ) extract( $data, EXTR_SKIP );
        ob_start();
        include $template;
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    /**
     * Handle the WordPress 'init' action.
     */
    public function handleWpAction_init() {
        add_shortcode('page_permalink', array($this, 'handleWpShortcode_page_permalink'));
        add_shortcode('page_title', array($this, 'handleWpShortcode_page_title'));
        add_shortcode('page_content', array($this, 'handleWpShortcode_page_content'));
        add_shortcode('page_meta', array($this, 'handleWpShortcode_page_meta'));
        add_shortcode('page_list', array($this, 'handleWpShortcode_page_list'));
    }
    
    /**
     * We only ever want one instance of our plugin loaded.
     */
    static public function SINGLETON($pluginFile) {
        if ( self::$INSTANCE === null ) {
            self::$INSTANCE = new PageShortcodesPlugin($pluginFile);
        }
        return self::$INSTANCE;
    }

}

?>