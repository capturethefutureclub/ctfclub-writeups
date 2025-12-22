<?php
/**
 * The main template file
 *
 * @package My_Theme
 */

get_header();
?>

<!-- Hero Section: Modern & Bold -->
<div class="relative py-32 overflow-hidden bg-modern-dark">
    <!-- Abstract Modern Background -->
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-modern-dark via-modern-dark to-modern-teal/30"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-modern-crimson/20 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-modern-gold/10 rounded-full blur-[100px] translate-y-1/3 -translate-x-1/4"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10 text-center">
        <span class="inline-block py-1 px-4 rounded-full bg-white/10 backdrop-blur-md border border-white/10 text-modern-gold text-xs font-bold tracking-[0.2em] uppercase mb-8">
            The Tinselwick Chronicles
        </span>
        
        <h1 class="text-6xl md:text-8xl font-serif font-bold mb-8 leading-tight text-white tracking-tight">
            Unravel the <br/>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-modern-crimson to-modern-gold">Winter Mystery</span>
        </h1>
        
        <p class="text-xl text-white/60 max-w-2xl mx-auto font-light leading-relaxed mb-10">
            Welcome to the Tinselwick Chronicles.
        </p>

        <div class="flex justify-center gap-4">
            <a href="#latest-stories" class="px-8 py-4 rounded-full bg-white text-modern-dark font-bold hover:bg-modern-gold transition-colors duration-300 shadow-xl shadow-white/10">
                Start Reading
            </a>
        </div>
    </div>
</div>

<main id="latest-stories" class="site-main flex-grow py-24 bg-snow-mist">
    <div class="container mx-auto px-6">
        <div class="flex justify-between items-end mb-12">
            <h2 class="text-3xl font-serif font-bold text-modern-dark">Latest Clues</h2>
            <div class="h-px flex-grow mx-8 bg-gray-200"></div>
            <span class="text-sm font-bold text-modern-crimson uppercase tracking-widest">Season 2025</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            if (have_posts()) :
                while (have_posts()) :
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('group bg-white rounded-3xl p-0 shadow-card hover:shadow-glow transition-all duration-500 border border-gray-100 flex flex-col h-full relative overflow-hidden'); ?>>
                        <!-- Hover Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-br from-modern-crimson/5 to-modern-gold/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none z-0"></div>

                        <?php 
                        $post_image = my_theme_get_post_image_url();
                        if ($post_image) : 
                        ?>
                        <div class="relative h-64 overflow-hidden">
                            <img src="<?php echo esc_url($post_image); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-60"></div>
                        </div>
                        <?php endif; ?>

                        <div class="p-8 flex flex-col flex-grow relative z-10">
                            <header class="entry-header mb-6">
                                <div class="flex items-center gap-3 text-xs font-bold text-modern-teal uppercase tracking-widest mb-4">
                                    <span class="w-2 h-2 rounded-full bg-modern-teal"></span>
                                    <?php echo get_the_date('M d, Y'); ?>
                                </div>
                                
                                <h2 class="post-title text-2xl font-serif font-bold leading-snug text-modern-dark group-hover:text-modern-crimson transition-colors">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h2>
                            </header>
                            
                            <div class="post-content text-slate-500 leading-relaxed mb-8 flex-grow">
                                <?php the_excerpt(); ?>
                            </div>

                            <footer class="entry-footer pt-6 border-t border-gray-100 flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-modern-dark text-white flex items-center justify-center font-serif font-bold text-xs">
                                        <?php echo substr(get_the_author(), 0, 1); ?>
                                    </div>
                                    <span class="text-sm font-bold text-modern-slate">By <?php the_author(); ?></span>
                                </div>
                                
                                <a href="<?php the_permalink(); ?>" class="w-10 h-10 rounded-full bg-modern-slate/5 flex items-center justify-center text-modern-dark group-hover:bg-modern-crimson group-hover:text-white transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            </footer>
                        </div>
                    </article>
                    <?php
                endwhile;
                
                the_posts_navigation();
            else :
                ?>
                <div class="col-span-full text-center py-20">
                    <p class="text-2xl font-serif text-gray-400">The signal is lost... no stories found.</p>
                </div>
                <?php
            endif;
            ?>
        </div>
    </div>
</main>

<?php
get_footer();
