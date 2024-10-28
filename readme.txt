=== Atom Builder ===
Contributors: vincentdubroeucq
Tags: widget, widgets, widget area, widgets area, sidebar, custom widgets, page builder, layout, content
Requires at least: 4.7
Tested up to: 5.3.2
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Build your page content with widgets, directly in the customizer. Simple. No page builder needed.

== Description ==
                
This plugin simply allows you to build your page content with widgets instead of using the default page layout for your theme.

**Using the Atom Builder**

By default, only basic pages are supported. It basically registers a widget area for all of your pages.
To avoir cluttering the admin area, the newly registered widget areas do not appear alongside your theme's standard ones, and you have to access them directly in the customizer.
To see it in action :
* Just visit any page on your site while logged in, and click the 'Customize' link. 
* In the Customizer panel, click on 'Widgets' and you should see a widget area registered for your page.
* Just use the widgets you need to build your page content. 

This plugin provides you with 3 additional widgets to help you build your page with interesting content and layout: Atom Builder Page, Atom Builder Post, and Atom Builder Posts widgets.
These basic widgets will probably get a bit more complex and have more options in the future, and a few more custom widgets will be added later, but that's a start.

**Adding theme support for the Atom Builder**

By default, the Atom Builder replaces your page content using 'the_content' filter. That means any markup you have before, such as the title for example, will be kept. 
Only your content as it appears in the editor in the admin area is replaced.

If you want to replace the whole content for your page, you'll have to tweak your theme's code a little bit.

* Add theme support for the Atom builder in your child theme's functions.php file by simply adding this snippet.

```
add_action( 'after_setup_theme', 'mythemeprefix_add_atom_builder_support' );
/**
 * Add theme support for the Atom Builder
 * This deactivate the basic filter on the_content. 
 * Just replace your get_template_part() call in page.php with atom_builder_get_template_part() to replace your whole page content template with registered widgets.
 **/
function mythemeprefix_add_atom_builder_support(){
    add_theme_support( 'atom-builder' );
}
```

* Duplicate the page.php template from your theme in your child theme's folder.
* Replace the `get_template_part()` function call with `atom_builder_get_template_part()`, with the same parameters. (Basically all you need to do is prefix it.)
* Now everything should work fine. Your whole template is replaced with widgets now, not just your content.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/atom-builder` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. That's it ! Edit your pages in the customizer and add widgets to your page to see it in action !

== Frequently Asked Questions ==

= Does the Atom Builder supports custom post types ? =

Not yet. By default, it only works with basic pages. But there's a hook for that ! Developper documentation is in writing, don't worry.
Have a look in the `init-functions.php` file in the `inc/` folder to see how it works.  

= Do you plan on releasing other widgets ? =

Of course ! The three widgets included are just a start.

== Screenshots ==

1. Once the plugin is activated, you'll get three new widgets. Registered widget areas for your pages won't appear.
2. Visit a basic page on the front end of your site, and open up the customizer to see a new widget area registered for your page.
3. Use the provided widgets or any other widget to build your page layout and content ! 

== Changelog ==

= 1.0.0 =
* Tested with WordPress 5.0.1

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
* Tested with WordPress 5.0.1

= 1.0 =
Initial release
