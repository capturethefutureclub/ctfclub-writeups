<?php
/**
 * The template for displaying comments
 *
 * @package My_Theme
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    <?php
    if (have_comments()) :
        ?>
        <h2 class="comments-title text-2xl font-serif font-bold text-modern-dark mb-8 flex items-center gap-3">
            <span class="text-3xl">💬</span>
            <?php
            $my_theme_comment_count = get_comments_number();
            if ('1' === $my_theme_comment_count) {
                printf(
                    /* translators: 1: title. */
                    esc_html__('One thought on &ldquo;%1$s&rdquo;', 'my-theme'),
                    '<span>' . get_the_title() . '</span>'
                );
            } else {
                printf( 
                    /* translators: 1: comment count number, 2: title. */
                    esc_html(_nx('%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $my_theme_comment_count, 'comments title', 'my-theme')),
                    number_format_i18n($my_theme_comment_count),
                    '<span>' . get_the_title() . '</span>'
                );
            }
            ?>
        </h2>

        <div class="comment-list space-y-6">
            <?php
            wp_list_comments(array(
                'style'      => 'div',
                'short_ping' => true,
                'callback'   => 'my_theme_comment',
                'avatar_size'=> 64,
            ));
            ?>
        </div>

        <?php
        the_comments_navigation();

        // If comments are closed and there are comments, let's leave a little note, shall we?
        if (!comments_open()) :
            ?>
            <p class="no-comments text-gray-500 italic mt-8"><?php esc_html_e('Comments are closed.', 'my-theme'); ?></p>
            <?php
        endif;

    endif; // Check for have_comments().

    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');

    $fields = array(
        'author' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">' .
                    '<div>' .
                    '<label for="author" class="block text-sm font-bold text-modern-dark mb-2 uppercase tracking-wide">Name' . ($req ? ' <span class="text-modern-crimson">*</span>' : '') . '</label> ' .
                    '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-modern-crimson focus:ring-2 focus:ring-modern-crimson/20 outline-none transition-all" placeholder="John Doe" />' .
                    '</div>',

        'email'  => '<div>' .
                    '<label for="email" class="block text-sm font-bold text-modern-dark mb-2 uppercase tracking-wide">Email' . ($req ? ' <span class="text-modern-crimson">*</span>' : '') . '</label> ' .
                    '<input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . ' class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-modern-crimson focus:ring-2 focus:ring-modern-crimson/20 outline-none transition-all" placeholder="john@example.com" />' .
                    '</div>' .
                    '</div>',

        'url'    => '<div class="mb-6">' .
                    '<label for="url" class="block text-sm font-bold text-modern-dark mb-2 uppercase tracking-wide">Website</label>' .
                    '<input id="url" name="url" type="url" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-modern-crimson focus:ring-2 focus:ring-modern-crimson/20 outline-none transition-all" placeholder="https://example.com" />' .
                    '</div>',
    );

    comment_form(array(
        'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title text-2xl font-serif font-bold text-modern-dark mb-6 mt-12">',
        'title_reply_after'  => '</h2>',
        'fields'             => $fields,
        'comment_field'      => '<div class="mb-6">' .
                                '<label for="comment" class="block text-sm font-bold text-modern-dark mb-2 uppercase tracking-wide">Comment</label>' .
                                '<textarea id="comment" name="comment" cols="45" rows="5" aria-required="true" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-modern-crimson focus:ring-2 focus:ring-modern-crimson/20 outline-none transition-all resize-y" placeholder="Share your thoughts..."></textarea>' .
                                '</div>',
        'submit_button'      => '<button name="%1$s" type="submit" id="%2$s" class="%3$s px-8 py-3 bg-modern-dark text-white font-bold rounded-full hover:bg-modern-crimson transition-colors duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">%4$s</button>',
        'class_submit'       => 'submit',
    ));
    ?>

</div><!-- #comments -->
