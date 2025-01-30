import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    safelist: [
        {
            pattern: /(text|bg|border)-(red|indigo|green)-(100|300|500|800)/,
            variants: ['focus', 'hover', 'active']
        },
        { 
            pattern: /^(w|h|my|mx|p|m|mb)-(\d+|\d+\/\d+|full|screen|auto|min|max|fit)$/ 
        },
        {
            pattern: /^(text|shadow)-(sm|md|lg|2xl|inner)$/,
        },
        {
            pattern: /^(top|left|rigth|bottom|inset|z)-(\d+|\d\/\d|auto)$/
        },
        { 
            pattern: /^(border)(-[trblxy])?-(\d+|0)$/ 
        },
        { 
            pattern: /^columns-(\d+)$/
        },
        { 
            pattern: /^(content|self|justify)-(\w+)$/
        },
        { 
            pattern: /^(overflow)-(\w+)$/
        },
        { 
            pattern: /grid-cols-(\d)/
        },

        'relative', 'absolute', 'fixed', 'sticky',
        
        'italic',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
};
