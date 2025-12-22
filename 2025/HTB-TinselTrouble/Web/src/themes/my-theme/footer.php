<?php
/**
 * The template for displaying the footer
 *
 * @package My_Theme
 */
?>

<footer id="colophon" class="site-footer bg-modern-dark text-white pt-24 pb-12 relative overflow-hidden">
    <!-- Subtle Gradient Overlay -->
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-modern-crimson via-modern-gold to-modern-teal"></div>
    
    <div class="container mx-auto px-6 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-20">
            <!-- Brand -->
            <div class="col-span-1 md:col-span-1">
                <h3 class="text-2xl font-serif font-bold text-white mb-6 flex items-center gap-2">
                    <span class="text-3xl">❄️</span>
                    Tinselwick
                </h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-6">
                    A modern chronicle of winter mysteries and restored magic. Restoring the Starshard Bauble, one story at a time.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white hover:bg-modern-crimson transition-all">
                        <span class="sr-only">Twitter</span>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white hover:bg-modern-crimson transition-all">
                        <span class="sr-only">GitHub</span>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" /></svg>
                    </a>
                </div>
            </div>

            <!-- Links -->
            <div>
                <h4 class="text-sm font-bold uppercase tracking-widest text-white mb-6">Explore</h4>
                <ul class="space-y-4 text-gray-400 text-sm">
                    <li><a href="#" class="hover:text-modern-crimson transition-colors">The Great Snowglobe</a></li>
                    <li><a href="#" class="hover:text-modern-crimson transition-colors">Archive 2024</a></li>
                    <li><a href="#" class="hover:text-modern-crimson transition-colors">Bauble Status</a></li>
                    <li><a href="#" class="hover:text-modern-crimson transition-colors">About Tinselwick</a></li>
                </ul>
            </div>

            <!-- Links -->
            <div>
                <h4 class="text-sm font-bold uppercase tracking-widest text-white mb-6">Support</h4>
                <ul class="space-y-4 text-gray-400 text-sm">
                    <li><a href="#" class="hover:text-modern-crimson transition-colors">Report a Glitch</a></li>
                    <li><a href="#" class="hover:text-modern-crimson transition-colors">Contact Santa</a></li>
                    <li><a href="#" class="hover:text-modern-crimson transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-modern-crimson transition-colors">Terms of Magic</a></li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div>
                <h4 class="text-sm font-bold uppercase tracking-widest text-white mb-6">Stay Updated</h4>
                <p class="text-gray-400 text-sm mb-4">Get the latest clues delivered to your inbox.</p>
                <form class="flex flex-col gap-3">
                    <input type="email" placeholder="email@example.com" class="bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-modern-crimson focus:ring-1 focus:ring-modern-crimson transition-all">
                    <button type="submit" class="bg-white text-modern-dark font-bold py-3 px-6 rounded-lg hover:bg-modern-crimson hover:text-white transition-all duration-300 shadow-lg">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="border-t border-white/5 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-sm text-gray-500">
                &copy; <?php echo date('Y'); ?> Tinselwick Chronicles.
            </div>
            <div class="flex gap-6 text-sm font-bold text-gray-500">
                <span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-green-500"></span> Systems Normal</span>
            </div>
        </div>
    </div>
</footer>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
