<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Foxtail
 */

get_header();
?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">

    <section class="ft-404 ft-l-content">
      <div class=" ft-l-container">
        <header class="page-header">
          <h1 class="page-title"><?php esc_html_e( 'Sorry, we can&rsquo;t find that page.', 'foxtail' ); ?></h1>
          <p>We’re all about a healthy internet but sometimes broken URLs happen.</p>
        </header><!-- .page-header -->
        <div class="page-content">
          <a href="<?php echo home_url() ?>" class="ft-button-secondary">
            Return Home
          </a>

        </div><!-- .page-content -->
      </div>
    </section><!-- .error-404 -->
  </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();