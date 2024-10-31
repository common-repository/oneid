# OneID WordPress Plugin

The official WordPress Plugin for OneID from OneID Limited. Allows WooCommerce store owners to verify the age of their customers before purchase.

## Key Features

**Certified**: OneID age checks meet all legal requirements, protecting you and your business.

**Complete Market**: Anyone with an online UK bank account can be verified by OneID giving you great market coverage.

**No Hassle**: Customers verify their age quickly, with no live selfies or document uploads.

## About OneID

Be confident you’re meeting legal requirements with OneID, a fully regulated online identity and age verification service.

Our fast, simple, and secure identity checks let you verify the age of your customers in real-time before they’ve placed their order - saving you time and hassle, and meeting legal requirements.

## How does it work?

Whenever you need to verify the age of your customers, whether that’s because you operate in a regulated industry, or you want to tailor your services to different age groups, OneID can help.

Instead of relying on customers to check a box, enter their birthday, or go through a lengthy document upload process, OneID lets your customers verify themselves in real-time, before their purchase, and in a matter of seconds.

In just a few clicks, customers can verify they are eligible to buy age-restricted goods via the information held at their bank. You can rest easy knowing your customers are protected, your business is protected, and your customer experience remains hassle-free!

OneID allows you to easily conduct legally compliant age verification in seconds for WooCommerce stores selling age-restricted products such as:

* Alcohol

* E-Cigarettes and Vapes

* Kitchenware (Knives)

* Pharmaceuticals

* Fireworks

* Pets and live animals

* DVDs and Games

* Home improvement and DIY tools (knives, tools, corrosive or noxious substances)

* Gardening (tools, sharp objects, noxious substances and some seeds)

* And any other products/ services you’d like to restrict access to.

## Competitive Pricing

Our age checks are free!

## Painless Integration

Install the plugin, sign up to register for access, and you are ready to go.

## Only Share Necessary Data

Customers only share the data that proves they’re over a certain age.

## Legally Compliant

Meet all current legal requirements for your products and services.

## Complete Market Coverage

Age check anyone with an online UK bank account.

## Hassle-Free

No document uploads, live selfies, or manual checks are required.

## Signing Up

You have the opportunity to trial the product first - for free and with no obligation to buy - [contact us](mailto:askus@oneid.uk?subject=WooCommerce%20OneID%20Sandbox%20Access%20Sign%20Up%20Request) to register for Sandbox-only access.
Once you have completed trialing and are ready to progress, [contact us](mailto:askus@oneid.uk?subject=WooCommerce%20OneID%20Production%20Access%20Sign%20Up%20Request) to register for Production access.

## Frequently Asked Questions

### How Do I Configure Age Restricted Products?

The plugin comes with one pre-set age restriction tag that can be assigned to products using the Products tab on the WordPress admin dashboard. These age restrictions are:
* Age Over 18

### Are these the only Age Restrictions that I can apply?

At present, yes, however, we are working on future improvements to allow for a greater range of age restrictions to be configured.

### Can I Test this Service?

Yes, using the OneID plugin tab on the WordPress admin dashboard, you can toggle the integration between our Production and Sandbox environments. The Sandbox integration is set up with a Model Bank and test users so that you can mock up the process before integrating with the live service.

### What if a Customer Fails to Verify Their Age?

Customers can choose to skip, may fail, or may be unable to complete, verification on the Checkout. By default, however, they will still be able to progress with their purchase, at which point the merchant’s standard process should be used as a fallback.

### Can I Configure the Prompts on my Site’s Front-End?

Merchants can manually add a shortcode to show age restriction prompts on Product pages, in any of the sections that accept shortcodes. When the Age Verification service is enabled, prompts will always show on carts containing age-restricted products, and the OneID button will always appear on the checkout. The location and appearance of these prompts are fixed.

## Screenshots

*to follow*

## Customisation options

### Shortcodes

| Shortcode                        | Description                                                                                                                                            |
|----------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------|
| `[oneid_age_restriction_notice]` | Display a notice on the product page that the current product is an age restricted product. It will include the age that the product is restricted to. |

### Hooks

| Hook                                     | Type | Description                                                                                                                                                                                                                                                   |
|------------------------------------------|------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `oneid_age_restriction_notice_cart_hook` | Filter | Which hook to use on the cart page for displaying the age restriction notice. _Default:_ `woocommerce_before_cart`                                                                                                                                            |
| `oneid_button_checkout_hook` | Filter | Which hook to use on the checkout page for displaying the OneID button. _Default:_ `woocommerce_before_checkout_form`                                                                                                                                         |
| `oneid_age_restriction_notice_show_on_product_page` | Filter | Should the age restriction notice show on the product page by default, i.e. without the need for a shortcode. _Default:_ `false`                                                                                                                              |
| `oneid_product_age_restriction_notice_priority` | Filter | Priority of the product page age restriction notice should the above hook be `true`. _Default:_ `40`                                                                                                                                                          |
| `oneid_uninstall_remove_age_verification_session_data` | Filter | Age verification data is stored inside the woocommerce session data, use this hook to have this removed during plugin uninstall. We don't do this by default as it could cause uninstall to timeout if store has a lot of active sessions. _Default:_ `false` |
| `oneid_uninstall` | Action | Called during plugin uninstall. |
| `woocommerce_order_status_pending_to_oneid-pending-av` | Action | Called when while placing the order, oneid verification skpped. |
