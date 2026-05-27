<?php
namespace WpBlueprint\App\Core\Support;

use WpBlueprint\App\Core\Helpers\Pagination;

if (!defined('ABSPATH')) exit;

/**
 * Class BaseModel
 *
 * Base class for WordPress Custom Post Type models.
 * Provides fluent querying, ACF support, pagination, meta filtering, and iteration.
 */
abstract class BaseModel implements \IteratorAggregate {

    // Basic post properties
    public $id;
    public $title;
    public $content;
    public $url;
    public $date;
    public $featureImage;
    public $category;          // Display-ready category name (populated by controller)
    public $acf = [];          // Stores all loaded ACF fields
    public $pagination;        // Stores pagination HTML

    // Static properties shared across all instances of the model
    protected static $query_args = [];  // WP_Query arguments for fluent query
    protected static $acf_fields = [];  // ACF fields to load
    protected static $meta_query = [];  // Meta filters for queries
    protected static $post_type;        // Must be defined in child model

    // Stores queried posts internally for iteration
    protected $posts = [];

    /**
     * Base constructor
     *
     * @param \WP_Post|null $post
     * @param array $acf_fields Optional list of ACF fields to load. If empty, all ACF fields are loaded.
     */
    public function __construct($post = null, $acf_fields = []) {
        if (!$post) return;

        $this->id      = $post->ID;
        $this->title   = get_the_title($post);
        $this->content = apply_filters('the_content', $post->post_content);
        $this->url     = get_permalink($post);
        $this->date    = get_the_date('', $post);
        $this->featureImage = get_the_post_thumbnail_url($post, 'full');

        // Auto-load all ACF fields if none specified (useful for single pages)
        if (empty($acf_fields)) {
            $acf_fields = array_keys(get_fields($post->ID) ?: []);
        }

        foreach ($acf_fields as $field) {
            $this->acf[$field] = get_field($field, $post->ID);
        }
    }

    /**
     * Start a new query
     *
     * @return static
     */
    public static function query(): static {
        static::$query_args = [
            'post_type' => static::$post_type,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ];
        static::$acf_fields = [];
        static::$meta_query = [];
        return new static();
    }

    /**
     * Add a simple "where" condition (currently only supports status)
     *
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function where(string $key, $value): static {
        if ($key === 'status' && $value === 'publish') {
            static::$query_args['post_status'] = 'publish';
        }
        return $this;
    }

    /**
     * Filter by post meta (ACF fields or custom meta)
     *
     * @param string $key Meta key
     * @param mixed $value Meta value
     * @param string $compare Comparison operator (default '=')
     * @return static
     */
    public function withMeta(string $key, $value, string $compare = '='): static {
        static::$meta_query[] = [
            'key' => $key,
            'value' => $value,
            'compare' => $compare,
        ];
        return $this;
    }

    /**
     * Apply a category-style filter from $_GET to the current query.
     *
     * The ACF field name is passed in by the caller so BaseModel never
     * hard-codes a project-specific field. Submitted values are whitelisted
     * against the field's actual ACF choices, so payloads that aren't valid
     * choices (including injection attempts) are dropped silently.
     *
     * @param string      $acf_field Name of the ACF radio/select field (e.g. 'acf_category')
     * @param string|null $get_param Optional query-string key; defaults to $acf_field
     */
    public function filterBy(string $acf_field, ?string $get_param = null): static {
        $param = $get_param ?? $acf_field;
        if (!isset($_GET[$param])) return $this;

        $value = sanitize_text_field(wp_unslash($_GET[$param]));
        if ($value === '') return $this;

        $choices = static::categories($acf_field);
        // Only validate when we actually retrieved choices; otherwise trust
        // sanitize_text_field so the filter still works on a freshly-installed
        // site that has no posts yet.
        if (!empty($choices) && !array_key_exists($value, $choices)) return $this;

        return $this->withMeta($acf_field, $value, '=');
    }

    /**
     * Apply a taxonomy filter from $_GET to the current query.
     *
     * For ACF "Taxonomy" fields with "Save Terms" enabled — the picked term
     * is attached to the post like a normal taxonomy, so we filter via
     * tax_query (NOT meta_query). Submitted slugs are validated with
     * term_exists() so unknown / injected values are dropped.
     *
     * @param string      $taxonomy  Taxonomy slug (e.g. 'acf-news-cat')
     * @param string|null $get_param Optional query-string key; defaults to $taxonomy
     */
    public function filterByTax(string $taxonomy, ?string $get_param = null): static {
        $param = $get_param ?? $taxonomy;
        if (!isset($_GET[$param])) return $this;

        $slug = sanitize_title(wp_unslash($_GET[$param]));
        if ($slug === '' || !term_exists($slug, $taxonomy)) return $this;

        $tax_query   = static::$query_args['tax_query'] ?? [];
        $tax_query[] = [
            'taxonomy' => $taxonomy,
            'field'    => 'slug',
            'terms'    => $slug,
        ];
        static::$query_args['tax_query'] = $tax_query;

        return $this;
    }

    /**
     * Specify which ACF fields to load
     *
     * @param array $fields
     * @return static
     */
    public function with(array $fields): static {
        static::$acf_fields = $fields;
        return $this;
    }

    /**
     * Limit number of posts (without pagination)
     *
     * @param int $number
     * @return static
     */
    public function limit(int $number): static {
        static::$query_args['posts_per_page'] = $number;

        if (!empty(static::$meta_query)) {
            static::$query_args['meta_query'] = static::$meta_query;
        }

        $query = new \WP_Query(static::$query_args);

        $this->posts = array_map(fn($p) => new static($p, static::$acf_fields), $query->posts);

        return $this;
    }

    /**
     * Paginate results
     *
     * @param int $per_page Number of posts per page
     * @return static
     */
    
    public function paginate(int $per_page = 10): static {
        $paged = max(1, get_query_var('paged', 1));
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $paged = intval($_GET['page']);
        }

        static::$query_args['posts_per_page'] = $per_page;
        static::$query_args['paged']          = $paged;

        if (!empty(static::$meta_query)) {
            static::$query_args['meta_query'] = static::$meta_query;
        }

        $query = new \WP_Query(static::$query_args);

        $this->posts = array_map(fn($p) => new static($p, static::$acf_fields), $query->posts);

        $this->pagination = Pagination::render($paged, $query->max_num_pages);

        return $this;
    }

    /**
     * Order posts by date descending
     *
     * @return static
     */
    public function latest(): static {
        static::$query_args['orderby'] = 'date';
        static::$query_args['order'] = 'DESC';
        return $this;
    }

    /**
     * Return the first post from the query
     *
     * @return static|null
     */
    public function first(): ?static {
        static::$query_args['posts_per_page'] = 1;
        $query = new \WP_Query(static::$query_args);

        if (!empty($query->posts)) {
            return new static($query->posts[0], static::$acf_fields);
        }

        return null;
    }


    /**
     * Find a single post by ID (auto-load all ACF fields)
     *
     * @param int $id
     * @param array $acf_fields Optional specific ACF fields to load
     * @return static|null
     */
    public static function find(int $id, array $acf_fields = []): ?static {
        $post = get_post($id);
        return $post ? new static($post, $acf_fields) : null;
    }

    /**
     * Return the ACF radio/select choices for a field on this CPT
     * as [value => label]. Useful for rendering a category filter UI.
     *
     * Reads the field config from a representative post of this CPT
     * (ACF needs a post context to resolve $field_name → $field_key).
     * Returns [] when the CPT has no posts yet or the field is missing.
     *
     * @param string $acf_field Name of the ACF radio/select field
     * @return array<string,string> [value => label]
     */
    public static function categories(string $acf_field): array {
        if (!function_exists('get_field_object')) return [];

        $sample = get_posts([
            'post_type'      => static::$post_type,
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ]);
        if (empty($sample)) return [];

        $field = get_field_object($acf_field, $sample[0]);
        return is_array($field) && !empty($field['choices']) ? $field['choices'] : [];
    }

    /**
     * Return the terms of a taxonomy as [slug => name].
     * Use this for ACF "Taxonomy" fields paired with filterByTax().
     *
     * @param string $taxonomy Taxonomy slug (e.g. 'acf-news-cat')
     * @return array<string,string> [slug => name]
     */
    public static function terms(string $taxonomy): array {
        if (!taxonomy_exists($taxonomy)) return [];

        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        ]);
        if (is_wp_error($terms) || empty($terms)) return [];

        $out = [];
        foreach ($terms as $term) {
            $out[$term->slug] = $term->name;
        }
        return $out;
    }

    /**
     * Allow foreach iteration over queried posts
     *
     * @return \Traversable
     */
    public function getIterator(): \Traversable {
        return new \ArrayIterator($this->posts);
    }
}
