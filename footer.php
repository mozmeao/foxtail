<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Foxtail
 */

?>

</div><!-- #content -->

<!-- Newsletter -->
<?php get_template_part('partials/newsletter'); ?>

<footer class="mzp-c-footer">
  <div class="mzp-l-content">
    <nav class="mzp-c-footer-primary">
      <div class="mzp-c-footer-primary-logo">
        <a href="https://www.mozilla.org/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral">Mozilla</a>
      </div>

      <div class="mzp-c-footer-sections">
        <section class="mzp-c-footer-section">
          <h5 class="mzp-c-footer-heading">Mozilla</h5>
          <ul class="mzp-c-footer-list">
            <li>
              <a href="https://www.mozilla.org/about/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral"><?php _e('About', 'foxtail'); ?></a>
            </li>
            <li>
              <a href="https://www.mozilla.org/contact/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral"><?php _e('Contact Us', 'foxtail'); ?></a>
            </li>
            <li>
              <a href="https://mozillafoundation.org/?form=blog-footer"><?php _e('Donate', 'foxtail'); ?></a>
            </li>
          </ul>
        </section>

        <section class="mzp-c-footer-section">
          <h5 class="mzp-c-footer-heading">Firefox</h5>
          <ul class="mzp-c-footer-list">
            <li>
              <a href="https://www.mozilla.org/firefox/new/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral"><?php _e('Download Firefox', 'foxtail'); ?></a>
            </li>
            <li>
              <a href="https://www.mozilla.org/firefox/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral"><?php _e('Desktop', 'foxtail'); ?></a>
            </li>
            <li>
              <a href="https://www.mozilla.org/firefox/mobile/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral"><?php _e('Mobile', 'foxtail'); ?></a>
            </li>
            <li>
              <a href="https://www.mozilla.org/firefox/features/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral"><?php _e('Features', 'foxtail'); ?></a>
            </li>
            <li>
              <a href="https://www.mozilla.org/firefox/channel/desktop/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral"><?php _e('Beta, Nightly, Developer Edition', 'foxtail'); ?></a>
            </li>
          </ul>
        </section>

        <section class="mzp-c-footer-section">
          <h5 class="mzp-c-footer-heading"><?php _e('Resources', 'foxtail'); ?></h5>
          <ul class="mzp-c-footer-list">
            <li>
              <a href="https://www.mozilla.org/privacy/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral"><?php _e('Privacy Hub', 'foxtail'); ?></a>
            </li>
            <li>
              <a href="https://www.mozilla.org/firefox/browsers/compare/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral"><?php _e('Browser Comparison', 'foxtail'); ?></a>
            </li>
            <li>
              <a href="https://mozilla.design/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral"><?php _e('Brand Standards', 'foxtail'); ?></a>
            </li>
          </ul>
        </section>

        <section class="mzp-c-footer-section">
          <h5 class="mzp-c-footer-heading"><?php _e('Social', 'foxtail'); ?></h5>
          <p class="ft-c-footer-social-header"><?php _e('Follow @Firefox', 'foxtail'); ?></p>
          <ul class="ft-c-footer-social">
            <li><a class="twitter" href="https://twitter.com/firefox">Twitter<span> (@mozilla)</span></a></li>
            <li><a class="instagram" href="https://www.instagram.com/firefox/">Instagram<span> (@mozilla)</span></a></li>
            <li><a class="youtube" href="https://www.youtube.com/user/firefoxchannel">YouTube<span> (@firefoxchannel)</span></a></li>
          </ul>
          <p class="ft-c-footer-social-header"><?php _e('Follow @Mozilla', 'foxtail'); ?></p>
          <ul class="ft-c-footer-social">
            <li><a class="twitter" href="https://twitter.com/mozilla">Twitter<span> (@mozilla)</span></a></li>
            <li><a class="instagram" href="https://www.instagram.com/mozilla/">Instagram<span> (@mozilla)</span></a></li>
          </ul>
        </section>
      </div>
    </nav>

    <nav class="mzp-c-footer-secondary">
      <div class="mzp-c-footer-legal">
        <ul class="mzp-c-footer-terms">
          <li>
            <a href="https://www.mozilla.org/privacy/websites/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral"><?php _e('Website Privacy Notice', 'foxtail'); ?></a>
          </li>
          <li>
            <a href="https://www.mozilla.org/privacy/websites/#cookies?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral"><?php _e('Cookies', 'foxtail'); ?></a>
          </li>
          <li>
            <a href="https://www.mozilla.org/about/legal/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral"><?php _e('Legal', 'foxtail'); ?></a>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>
