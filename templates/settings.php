<?php
/**
 * @package Language detector
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 * @var $this \gplcart\core\controllers\backend\Controller
 * To see available variables <?php print_r(get_defined_vars()); ?>
 */
?>
<form method="post" class="form-horizontal">
  <input type="hidden" name="token" value="<?php echo $_token; ?>">
  <div class="form-group">
    <?php foreach ($languages as $column => $items) { ?>
    <div class="col-md-<?php echo 12 / count($languages); ?>">
      <?php if ($column == 0) { ?>
      <input type="checkbox" onchange="Gplcart.selectAll(this, 'settings[redirect][]');"> <?php echo $this->text('Select all'); ?>
      <?php } ?>
      <?php foreach ($items as $code => $language) { ?>
      <div class="checkbox">
        <label>
          <input name="settings[redirect][]" type="checkbox" value="<?php echo $this->e($code); ?>"<?php echo in_array($code, $settings['redirect']) ? ' checked' : ''; ?>> <?php echo $this->text($language['name']); ?>
        </label>
      </div>
      <?php } ?>
    </div>
    <?php } ?>
  </div>
  <div class="help-block"><?php echo $this->text("Select languages for automatic redirection. If a user's language is in the selected languages, he/she will be redirected to that language"); ?></div>
  <div class="btn-toolbar">
    <a href="<?php echo $this->url("admin/module/list"); ?>" class="btn btn-default"><?php echo $this->text("Cancel"); ?></a>
    <button class="btn btn-default save" name="save" value="1"><?php echo $this->text("Save"); ?></button>
  </div>
</form>