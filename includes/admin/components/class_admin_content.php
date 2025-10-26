<?php
/**
 * Componente Content del Admin
 *
 * Renderiza el área de contenido con cards estilo Bentō
 *
 * @package WlandChat
 * @version 1.2.0
 */

namespace WlandChat\Admin;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Admin_Content {

    /**
     * Instancia única (Singleton)
     *
     * @var Admin_Content
     */
    private static $instance = null;

    /**
     * Obtener instancia única
     *
     * @return Admin_Content
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor privado (Singleton)
     */
    private function __construct() {
        // Inicialización si es necesaria
    }

    /**
     * Renderizar card estilo Bentō
     *
     * @param array $args Argumentos de la card
     * @return void
     */
    public function render_card($args = array()) {
        $defaults = array(
            'title' => '',
            'subtitle' => '',
            'description' => '',
            'content' => '',
            'icon' => '',
            'icon_color' => '#023e8a',
            'action_text' => '',
            'action_url' => '',
            'action_target' => '_self',
            'footer' => '',
            'custom_class' => '',
        );

        $args = wp_parse_args($args, $defaults);

        ?>
        <div class="wland-card <?php echo esc_attr($args['custom_class']); ?>">
            <?php if (!empty($args['icon'])): ?>
            <div class="wland-card__icon">
                <?php echo $args['icon']; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($args['title'])): ?>
            <h3 class="wland-card__title">
                <?php echo esc_html($args['title']); ?>
            </h3>
            <?php endif; ?>

            <?php if (!empty($args['subtitle'])): ?>
            <p class="wland-card__subtitle">
                <?php echo esc_html($args['subtitle']); ?>
            </p>
            <?php endif; ?>

            <?php if (!empty($args['description'])): ?>
            <p class="wland-card__description">
                <?php echo esc_html($args['description']); ?>
            </p>
            <?php endif; ?>

            <?php if (!empty($args['content'])): ?>
            <div class="wland-card__content">
                <?php
                // Allow form inputs in card content
                $allowed_html = array_merge(
                    wp_kses_allowed_html('post'),
                    array(
                        'input' => array(
                            'type' => true,
                            'id' => true,
                            'name' => true,
                            'value' => true,
                            'class' => true,
                            'style' => true,
                            'placeholder' => true,
                            'checked' => true,
                            'autocomplete' => true,
                            'rows' => true,
                            'cols' => true,
                        ),
                        'textarea' => array(
                            'id' => true,
                            'name' => true,
                            'class' => true,
                            'style' => true,
                            'placeholder' => true,
                            'rows' => true,
                            'cols' => true,
                        ),
                        'select' => array(
                            'id' => true,
                            'name' => true,
                            'class' => true,
                            'style' => true,
                            'multiple' => true,
                            'size' => true,
                        ),
                        'option' => array(
                            'value' => true,
                            'selected' => true,
                        ),
                        'label' => array(
                            'for' => true,
                            'class' => true,
                        ),
                        'span' => array(
                            'class' => true,
                        ),
                        'p' => array(
                            'class' => true,
                            'style' => true,
                        ),
                    )
                );
                echo wp_kses($args['content'], $allowed_html);
                ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($args['action_text']) && !empty($args['action_url'])): ?>
            <div class="wland-card__action">
                <a href="<?php echo esc_url($args['action_url']); ?>"
                   class="wland-button wland-button--link"
                   target="<?php echo esc_attr($args['action_target']); ?>">
                    <?php echo esc_html($args['action_text']); ?>
                </a>
            </div>
            <?php endif; ?>

            <?php if (!empty($args['footer'])): ?>
            <div class="wland-card__footer">
                <?php echo wp_kses_post($args['footer']); ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Renderizar quick action button
     *
     * @param array $args Argumentos del botón
     * @return void
     */
    public function render_quick_action($args = array()) {
        $defaults = array(
            'text' => '',
            'url' => '',
            'icon' => '',
            'style' => 'primary', // primary, secondary, outline
            'target' => '_self',
            'custom_class' => '',
        );

        $args = wp_parse_args($args, $defaults);

        $button_class = 'wland-button wland-button--' . $args['style'];
        if (!empty($args['custom_class'])) {
            $button_class .= ' ' . $args['custom_class'];
        }

        ?>
        <a href="<?php echo esc_url($args['url']); ?>"
           class="<?php echo esc_attr($button_class); ?>"
           target="<?php echo esc_attr($args['target']); ?>">
            <?php if (!empty($args['icon'])): ?>
            <span class="wland-button__icon">
                <?php echo $args['icon']; ?>
            </span>
            <?php endif; ?>
            <span class="wland-button__text">
                <?php echo esc_html($args['text']); ?>
            </span>
        </a>
        <?php
    }

    /**
     * Renderizar sección con header
     *
     * @param array $args Argumentos de la sección
     * @return void
     */
    public function render_section($args = array()) {
        $defaults = array(
            'title' => '',
            'description' => '',
            'content' => '',
            'custom_class' => '',
        );

        $args = wp_parse_args($args, $defaults);

        ?>
        <div class="wland-section <?php echo esc_attr($args['custom_class']); ?>">
            <?php if (!empty($args['title'])): ?>
            <div class="wland-section__header">
                <h2 class="wland-section__title">
                    <?php echo esc_html($args['title']); ?>
                </h2>
                <?php if (!empty($args['description'])): ?>
                <p class="wland-section__description">
                    <?php echo esc_html($args['description']); ?>
                </p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($args['content'])): ?>
            <div class="wland-section__content">
                <?php echo wp_kses_post($args['content']); ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Renderizar toggle/switch moderno
     *
     * @param array $args Argumentos del toggle
     * @return void
     */
    public function render_toggle($args = array()) {
        $defaults = array(
            'id' => '',
            'name' => '',
            'label' => '',
            'description' => '',
            'checked' => false,
            'value' => '1',
            'custom_class' => '',
        );

        $args = wp_parse_args($args, $defaults);

        $toggle_id = !empty($args['id']) ? $args['id'] : 'toggle-' . uniqid();

        ?>
        <div class="wland-toggle <?php echo esc_attr($args['custom_class']); ?>">
            <div class="wland-toggle__control">
                <input
                    type="checkbox"
                    id="<?php echo esc_attr($toggle_id); ?>"
                    name="<?php echo esc_attr($args['name']); ?>"
                    value="<?php echo esc_attr($args['value']); ?>"
                    <?php checked($args['checked'], true); ?>
                    class="wland-toggle__input"
                />
                <label for="<?php echo esc_attr($toggle_id); ?>" class="wland-toggle__label">
                    <span class="wland-toggle__switch"></span>
                    <?php if (!empty($args['label'])): ?>
                    <span class="wland-toggle__text">
                        <?php echo esc_html($args['label']); ?>
                    </span>
                    <?php endif; ?>
                </label>
            </div>

            <?php if (!empty($args['description'])): ?>
            <p class="wland-toggle__description">
                <?php echo esc_html($args['description']); ?>
            </p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Renderizar grid de cards (layout Bento)
     *
     * @param array $cards Array de configuraciones de cards
     * @param int $columns Número de columnas (2, 3, 4)
     * @return void
     */
    public function render_card_grid($cards = array(), $columns = 3) {
        if (empty($cards)) {
            return;
        }

        $grid_class = 'wland-card-grid wland-card-grid--' . absint($columns) . '-cols';

        ?>
        <div class="<?php echo esc_attr($grid_class); ?>">
            <?php foreach ($cards as $card): ?>
                <?php $this->render_card($card); ?>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
