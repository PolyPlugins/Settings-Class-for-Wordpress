<?php
/**
 * Default settings template
 */

if (!defined('ABSPATH')) exit;
?>

<div class="bootstrap-wrapper">
  <!-- Display a loader as a placeholder until page is loaded -->
  <div class="load-settings d-flex justify-content-center m-3">
    <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>

  <!-- Settings are hidden until page loaded to prevent layout shifting -->
  <div class="row settings-container align-items-start" style="display: none;">

    <!-- Navigation -->
    <div class="col-lg-2 col-md-12 tabs">
      <nav class="nav flex-column">
        <img src="<?php echo isset($this->config['logo']) ? esc_url(plugins_url($this->config['logo'], $this->plugin)) : esc_url(plugins_url('/img/logo.png', $this->admin_panel)); ?>" class="nav-logo" />
      </nav>

      <nav class="nav flex-column">
        <?php foreach ($sections as $section) : ?>
          <?php 
            $icon      = isset($get_fields[$section]['icon']) ? $get_fields[$section]['icon'] : '';
            $label     = isset($get_fields[$section]['label']) ? $get_fields[$section]['label'] : ucfirst(str_replace('-', ' ', $section));
            $has_icons = false;
          ?>
          <a class="nav-link<?php echo ($sections[0] == $section) ? ' active' : ''; ?>" 
            href="#<?php echo esc_attr($section); ?>" 
            selected-section="<?php echo esc_attr($section); ?>">
            <?php if ($icon) : ?>
              <?php $has_icons = true; ?>
              <i class="bi bi-<?php echo esc_attr($icon); ?>"></i>
            <?php endif; ?>
            <?php echo esc_html($label); ?>
          </a>

          <?php if (!empty($get_fields[$section]['subsections'])) : ?>
            <?php foreach ($get_fields[$section]['subsections'] as $sub_id => $subsection) : ?>
              <?php 
                $sub_icon  = isset($subsection['icon']) ? $subsection['icon'] : '';
                $sub_label = isset($subsection['label']) ? $subsection['label'] : ucfirst(str_replace('-', ' ', $sub_id));
              ?>
              <a class="nav-link subsection-link ms-3" 
                href="#<?php echo esc_attr($section . '-' . $sub_id); ?>" 
                selected-section="<?php echo esc_attr($section . '-' . $sub_id); ?>">
                <?php if ($sub_icon) : ?>
                  <i class="bi bi-<?php echo esc_attr($sub_icon); ?>"></i>
                <?php endif; ?>
                <?php echo esc_html($sub_label); ?>
              </a>
            <?php endforeach; ?>
          <?php endif; ?>
        <?php endforeach; ?>

        <?php if (isset($this->config['support'])) : ?>
          <a class="nav-link" href="<?php echo esc_url($this->config['support']); ?>" target="_blank">
            <?php if ($has_icons) : ?>
              <i class="bi bi-chat-left-dots"></i>
            <?php endif; ?>
            Support
          </a>
        <?php endif; ?>
      </nav>
    </div>

    <!-- Fields -->
    <div class="<?php echo isset($this->config['sidebar']) ? 'col-lg-8 col-md-10' : 'col-lg-10 col-md-12'; ?> g-0">
      <?php foreach($get_fields as $section => $section_data) : ?>
        <?php $fields = isset($section_data['fields']) ? $section_data['fields'] : array(); ?>

        <!-- Parent Section -->
        <div class="options"<?php echo ($sections[0] != $section) ? ' style="display: none;"' : ''; ?> section="<?php echo esc_attr($section) ?>">
          <!-- Notes -->
          <?php if (isset($section_data['note']['message']) && $section_data['note']['message']) : ?>
            <div class="note<?php echo isset($section_data['note']['class']) && $section_data['note']['class'] ? " " . esc_html($section_data['note']['class']) :  " warning"; ?>">
              <?php echo " " . esc_html($section_data['note']['message']); ?>
            </div>
          <?php endif; ?>
          <div class="options-padding">
            <h2>
              <?php if (!empty($section_data['icon'])) : ?>
                <i class="<?php echo esc_attr($section_data['icon']); ?>"></i>
              <?php endif; ?>
              <?php echo esc_html(ucfirst($section)) . ' Settings'; ?>
            </h2>
            <div class="fields">
              <?php foreach($fields as $field) : ?>
                <?php $field['section'] = $section; ?>
                <?php $this->add_field($field); ?>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Subsections as independent tabs -->
        <?php if (!empty($section_data['subsections'])) : ?>
          <?php foreach($section_data['subsections'] as $sub_id => $subsection) : ?>
            <?php $sub_fields = isset($subsection['fields']) ? $subsection['fields'] : array(); ?>
            <div class="options" style="display:none;" section="<?php echo esc_attr($section . '-' . $sub_id); ?>">
              <!-- Notes -->
              <?php if (isset($section_data['note']['message']) && $section_data['note']['message']) : ?>
                <div class="note<?php echo isset($section_data['note']['class']) && $section_data['note']['class'] ? " " . esc_html($section_data['note']['class']) :  " warning"; ?>">
                  <?php echo " " . esc_html($section_data['note']['message']); ?>
                </div>
              <?php endif; ?>
              <div class="options-padding">
                <h2>
                  <?php if (!empty($subsection['icon'])) : ?>
                    <i class="<?php echo esc_attr($subsection['icon']); ?>"></i>
                  <?php endif; ?>
                  <?php echo esc_html($subsection['label'] ?? ucfirst($sub_id)) . ' Settings'; ?>
                </h2>
                <div class="fields">
                  <?php foreach($sub_fields as $field) : ?>
                    <?php $field['section'] = $section . '-' . $sub_id; ?>
                    <?php $this->add_field($field); ?>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      <?php endforeach; ?>
      
      <div class="row align-items-center">
        <div class="col-6">
          <?php submit_button(); ?>
        </div>
        <div class="col-6 text-end">
          <div class="credit">Built with <a href="https://github.com/PolyPlugins/Settings-Class-for-Wordpress">Settings Class for WordPress</a><span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-2 col-md-2 helper-sidebar" style="display: none;">
    <h2>Help<span class="helper-close"><a href="javascript:void(0)"><i class="bi bi-x-circle-fill"></i></a></span></h2>
    <div class="helper-text"></div>
  </div>
  <?php if (isset($this->config['sidebar'])) : ?>
    <?php
    $heading       = isset($this->config['sidebar']['heading']) ? sanitize_text_field($this->config['sidebar']['heading']) : '';
    $body          = isset($this->config['sidebar']['body']) ? sanitize_text_field($this->config['sidebar']['body']) : '';
    $button_label  = isset($this->config['sidebar']['button_label']) ? sanitize_text_field($this->config['sidebar']['button_label']) : '';
    $button_url    = isset($this->config['sidebar']['button_url']) ? sanitize_url($this->config['sidebar']['button_url']) : '';
    ?>
    <div class="col-lg-2 col-md-2 sidebar">
      <div class="sidebar-content">
        <?php if ($heading) : ?>
          <h2 class="sidebar-heading"><?php echo esc_html($heading); ?></h2>
        <?php endif; ?>
        
        <?php if ($body) : ?>
          <p class="sidebar-body"><?php echo esc_html($body); ?></p>
        <?php endif; ?>

        <?php if ($button_label && $button_url) : ?>
          <p class="sidebar-button"><a href="<?php echo esc_url($button_url); ?>" class="button button-primary" target="_blank"><?php echo esc_html($button_label); ?></a></p>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>