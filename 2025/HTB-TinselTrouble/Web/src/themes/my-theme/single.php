<?php
/**
 * The template for displaying single posts
 *
 * @package My_Theme
 */

get_header();
?>

<?php
/* Start the Loop */
while (have_posts()) :
    the_post();
    ?>

    <!-- Hero Section: Immersive & Clean -->
    <div class="relative py-32 bg-modern-dark overflow-hidden">
        <!-- Background Image & Gradients -->
        <?php 
        $post_image = my_theme_get_post_image_url();
        if ($post_image) : 
        ?>
        <div class="absolute inset-0 bg-cover bg-center opacity-40 mix-blend-overlay transition-opacity duration-1000" style="background-image: url('<?php echo esc_url($post_image); ?>');"></div>
        <?php endif; ?>
        
        <div class="absolute inset-0 bg-gradient-to-b from-modern-dark/95 via-modern-dark/80 to-modern-dark"></div>
        <div class="absolute top-0 right-0 w-full h-full bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-modern-crimson/20 via-transparent to-transparent opacity-50"></div>
        
        <div class="container mx-auto px-6 relative z-10 text-center">
            <div class="flex items-center justify-center gap-3 text-xs font-bold text-modern-gold uppercase tracking-[0.2em] mb-8">
                <span class="w-1 h-1 rounded-full bg-modern-gold"></span>
                <?php echo get_the_date('F j, Y'); ?>
                <span class="w-1 h-1 rounded-full bg-modern-gold"></span>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-serif font-bold mb-8 leading-tight text-white max-w-5xl mx-auto tracking-tight">
                <?php the_title(); ?>
            </h1>
            
            <div class="flex items-center justify-center gap-4 text-white/60">
                <div class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 backdrop-blur-sm">
                    <div class="w-6 h-6 rounded-full bg-modern-crimson flex items-center justify-center text-white font-serif font-bold text-xs">
                        <?php echo substr(get_the_author(), 0, 1); ?>
                    </div>
                    <span class="text-sm font-medium text-white">By <?php the_author(); ?></span>
                </div>
            </div>
        </div>
    </div>

    <main id="main" class="site-main flex-grow py-20 bg-snow-mist">
        <div class="container mx-auto px-6">
            <div class="max-w-3xl mx-auto">
                <article id="post-<?php the_ID(); ?>" <?php post_class('bg-white rounded-[2rem] p-10 md:p-16 shadow-xl border border-gray-100'); ?>>
                    
                    <div class="post-content text-modern-slate leading-loose text-lg font-light prose prose-lg max-w-none prose-headings:font-serif prose-headings:font-bold prose-headings:text-modern-dark prose-a:text-modern-crimson hover:prose-a:text-modern-dark prose-strong:text-modern-dark prose-blockquote:border-l-4 prose-blockquote:border-modern-gold prose-blockquote:pl-6 prose-blockquote:italic prose-blockquote:text-modern-dark/80">
                        <?php the_content(); ?>
                    </div>

                    <footer class="entry-footer mt-16 pt-10 border-t border-gray-100">
                        <div class="flex justify-between items-center">
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="text-modern-dark font-bold hover:text-modern-crimson transition-colors flex items-center gap-3 group text-sm uppercase tracking-wider">
                                <span class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center group-hover:bg-modern-crimson group-hover:text-white transition-all">←</span>
                                Back to Quest
                            </a>
                            
                            <div class="flex gap-4">
                                <button class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:border-modern-crimson hover:text-modern-crimson transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>
                                <button class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:border-modern-teal hover:text-modern-teal transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </footer>
                </article>
                
                <?php
                // If comments are open or we have at least one comment, load up the comment template.
                if (comments_open() || get_comments_number()) :
                    echo '<div class="mt-12 bg-white rounded-[2rem] p-10 md:p-16 shadow-lg border border-gray-100">';
                    comments_template();
                    echo '</div>';
                endif;
                ?>
            </div>
        </div>
    </main>

<?php
endwhile; // End of the loop.
?>

<?php
get_footer();
