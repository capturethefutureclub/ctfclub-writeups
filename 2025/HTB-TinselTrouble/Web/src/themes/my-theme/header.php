<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'modern-crimson': '#E11D48', // Vibrant Rose Red
                        'modern-dark': '#0F172A',    // Deep Slate/Midnight
                        'modern-slate': '#334155',   // Lighter Slate
                        'modern-gold': '#F59E0B',    // Amber Gold
                        'modern-teal': '#0F766E',    // Deep Teal
                        'snow-white': '#FFFFFF',
                        'snow-mist': '#F8FAFC',      // Very light blue-grey
                    },
                    fontFamily: {
                        'sans': ['"Outfit"', 'sans-serif'],
                        'serif': ['"Playfair Display"', 'serif'],
                    },
                    boxShadow: {
                        'glow': '0 0 20px rgba(225, 29, 72, 0.3)',
                        'card': '0 10px 30px -10px rgba(15, 23, 42, 0.1)',
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            background-color: #F8FAFC;
        }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .glass-dark {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .text-gradient {
            background: linear-gradient(135deg, #E11D48 0%, #F59E0B 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>

    <?php wp_head(); ?>
</head>

<body <?php body_class('text-modern-dark font-sans antialiased min-h-screen flex flex-col selection:bg-modern-crimson selection:text-white'); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site flex flex-col min-h-screen relative">
    
    <header id="masthead" class="site-header fixed w-full z-50 transition-all duration-300 glass-dark">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="site-branding">
                    <h1 class="site-title text-2xl font-serif font-bold tracking-tight">
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" class="text-white hover:text-modern-gold transition-colors duration-300 flex items-center gap-3">
                            <span class="text-3xl">❄️</span>
                            <?php bloginfo('name'); ?>
                        </a>
                    </h1>
                </div>
                
                <nav id="site-navigation" class="main-navigation hidden md:block">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'fallback_cb'    => false,
                        'container'      => false,
                        'menu_class'     => 'flex gap-8 text-sm font-bold uppercase tracking-widest text-white/90 hover:text-white',
                        'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    ));
                    ?>
                </nav>

            </div>
        </div>
    </header>
    
    <!-- Spacer -->
    <div class="h-20 bg-modern-dark"></div>
