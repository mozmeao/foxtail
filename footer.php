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
        <a href="https://www.mozilla.org/">Mozilla</a>
      </div>

      <div class="mzp-c-footer-sections">
        <section class="mzp-c-footer-section">
          <h5 class="mzp-c-footer-heading">Mozilla</h5>
          <ul class="mzp-c-footer-list">
            <li>
              <a
                href="https://www.mozilla.org/about/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral">About</a>
            </li>
            <li>
              <a
                href="https://www.mozilla.org/contact/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral">Contact
                Us</a>
            </li>
            <li>
              <a
                href="https://donate.mozilla.org/?presets=50,30,20,10&amount=30&currency=usd&utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral">Donate</a>
            </li>
          </ul>
        </section>

        <section class="mzp-c-footer-section">
          <h5 class="mzp-c-footer-heading">Firefox</h5>
          <ul class="mzp-c-footer-list">
            <li>
              <a
                href="https://www.mozilla.org/firefox/new/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral">Download
                Firefox</a>
            </li>
            <li>
              <a
                href="https://www.mozilla.org/firefox/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral">Desktop</a>
            </li>
            <li>
              <a
                href="https://www.mozilla.org/firefox/mobile/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral">Mobile</a>
            </li>
            <li>
              <a
                href="https://www.mozilla.org/firefox/features/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral">
                Features
              </a>
            </li>
            <li>
              <a
                href="https://www.mozilla.org/firefox/channel/desktop/?utm_source=blog.mozilla.org&utm_campaign=footer&utm_medium=referral">
                Beta, Nightly, Developer Edition
              </a>
            </li>
          </ul>
        </section>

        <section class="mzp-c-footer-section">
          <h5 class="mzp-c-footer-heading">Resources</h5>
          <ul class="mzp-c-footer-list">
            <li>
              <a href="https://www.mozilla.org/privacy/">Privacy Hub</a>
            </li>
            <li>
              <a href="https://www.mozilla.org/firefox/browsers/compare/">Browser Comparison</a>
            </li>
            <li>
              <a
                href="https://mozilla.design/?utm_source=www.mozilla.org&utm_medium=referral&utm_campaign=footer&utm_content=resources">Brand
                Standards</a>
            </li>
          </ul>
        </section>

        <section class="mzp-c-footer-section">
          <h5 class="mzp-c-footer-heading">Social</h5>
          <p class="ft-c-footer-social-header">Follow @Firefox</p>
          <ul class="ft-c-footer-social">
            <li><a class="twitter" href="https://twitter.com/firefox">Twitter<span> (@mozilla)</span></a></li>
            <li><a class="instagram" href="https://www.instagram.com/firefox/">Instagram<span> (@mozilla)</span></a>
            </li>
            <li><a class="youtube" href="https://www.youtube.com/user/firefoxchannel">YouTube<span>
                  (@firefoxchannel)</span></a></li>
          </ul>
          <p class="ft-c-footer-social-header">Follow @Mozilla</p>
          <ul class="ft-c-footer-social">
            <li><a class="twitter" href="https://twitter.com/mozilla">Twitter<span> (@mozilla)</span></a></li>
            <li><a class="instagram" href="https://www.instagram.com/mozilla/">Instagram<span> (@mozilla)</span></a>
            </li>
          </ul>
        </section>
      </div>
    </nav>

    <nav class="mzp-c-footer-secondary">

      <div class="mzp-c-footer-legal">
        <ul class="mzp-c-footer-terms">
          <li>
            <a href="https://www.mozilla.org/privacy/websites/">
              Website Privacy Notice
            </a>
          </li>
          <li>
            <a href="https://www.mozilla.org/privacy/websites/#cookies">
              Cookies
            </a>
          </li>
          <li>
            <a href="https://www.mozilla.org/about/legal/">Legal</a>
          </li>
          <li>
            <a href="https://www.mozilla.org/privacy/websites/">
              Website Privacy Notice
            </a>
          </li>
          <li>
            <a href="https://www.mozilla.org/privacy/websites/#cookies">
              Cookies
            </a>
          </li>
          <li>
            <a href="https://www.mozilla.org/about/legal/">Legal</a>
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