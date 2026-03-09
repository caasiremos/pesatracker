<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Privacy Policy" />

    <div class="min-h-svh bg-background">
        <header class="sticky top-0 z-10 border-b border-border bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
            <div class="mx-auto flex h-14 max-w-3xl items-center justify-between px-4 sm:px-6">
                <Link
                    :href="route('home')"
                    class="flex items-center gap-2 font-medium text-foreground transition-opacity hover:opacity-80"
                >
                    <AppLogoIcon class="size-8 fill-current" />
                    <span class="sr-only">Pesa Track</span>
                </Link>
                <Link
                    :href="route('home')"
                    class="text-sm text-muted-foreground underline-offset-4 hover:text-foreground hover:underline"
                >
                    Back
                </Link>
            </div>
        </header>

        <main class="mx-auto max-w-3xl px-4 py-10 sm:px-6 sm:py-12">
            <article class="prose-policy">
                <h1 class="mb-2 text-2xl font-semibold tracking-tight text-foreground sm:text-3xl">
                    Pesa Track Privacy Policy
                </h1>
                <p class="mb-8 text-sm text-muted-foreground"><strong>Last updated:</strong> March 10, 2025</p>

                <p class="mb-6 text-foreground">
                    Pesa Track ("we," "our," or "us") is a mobile application that helps you track recurring bills,
                    expenses, budgets, and payments. This Privacy Policy explains how we collect, use, store, and
                    protect your information when you use the Pesa Track app and related services.
                </p>
                <p class="mb-8 text-foreground">
                    By using Pesa Track, you agree to the practices described in this policy. If you do not agree,
                    please do not use the app.
                </p>

                <hr class="my-8 border-border" />

                <h2 class="mb-3 mt-8 text-lg font-semibold text-foreground">1. Information We Collect</h2>

                <h3 class="mb-2 mt-6 text-base font-medium text-foreground">1.1 Account and identity information</h3>
                <p class="mb-3 text-foreground">
                    When you register or use Pesa Track, we may collect:
                </p>
                <ul class="mb-6 list-inside list-disc space-y-1 text-foreground">
                    <li><strong>Name</strong></li>
                    <li><strong>Email address</strong></li>
                    <li><strong>Phone number</strong></li>
                    <li><strong>Password</strong> (stored in encrypted form on our servers)</li>
                    <li><strong>One-time verification codes (OTP)</strong> sent to your phone number for account verification and security</li>
                </ul>

                <h3 class="mb-2 mt-6 text-base font-medium text-foreground">1.2 Financial and transaction data</h3>
                <p class="mb-3 text-foreground">
                    To provide expense tracking, budgets, and payment features, we collect:
                </p>
                <ul class="mb-6 list-inside list-disc space-y-1 text-foreground">
                    <li><strong>Expenses:</strong> amounts, dates, categories, notes, and any attachments you add</li>
                    <li><strong>Budgets:</strong> budget amounts and categories</li>
                    <li><strong>Wallet and transfers:</strong> deposit and transfer amounts, and when you use mobile money (e.g. MoMo), the phone number and transaction details necessary to complete the payment</li>
                    <li><strong>Merchants and categories</strong> you create or use</li>
                </ul>

                <h3 class="mb-2 mt-6 text-base font-medium text-foreground">1.3 Device and app usage</h3>
                <ul class="mb-6 list-inside list-disc space-y-1 text-foreground">
                    <li><strong>Push notification token (FCM):</strong> We send your Firebase Cloud Messaging token to our servers so we can deliver push notifications (e.g. reminders, alerts). This token is linked to your account when you are logged in.</li>
                    <li><strong>Device identifiers:</strong> Firebase and our backend may receive device-related information necessary for sending notifications and securing your account.</li>
                </ul>

                <h3 class="mb-2 mt-6 text-base font-medium text-foreground">1.4 Data stored on your device</h3>
                <p class="mb-3 text-foreground">
                    The app stores the following on your device (e.g. via local storage):
                </p>
                <ul class="mb-6 list-inside list-disc space-y-1 text-foreground">
                    <li><strong>Login data:</strong> access token, your customer ID, name, email, phone number, and account status, so you can stay logged in</li>
                    <li><strong>Preference for local authentication:</strong> whether you have enabled biometric or device lock (e.g. fingerprint or face) to open the app</li>
                </ul>
                <p class="mb-8 text-foreground">
                    We do not collect or store your actual biometric data; the device handles authentication locally.
                </p>

                <hr class="my-8 border-border" />

                <h2 class="mb-3 mt-8 text-lg font-semibold text-foreground">2. How We Use Your Information</h2>
                <p class="mb-3 text-foreground">We use the information we collect to:</p>
                <ul class="mb-8 list-inside list-disc space-y-1 text-foreground">
                    <li>Create and manage your account and authenticate you (including via OTP)</li>
                    <li>Provide expense tracking, budgets, categories, and merchant features</li>
                    <li>Process wallet deposits, transfers, and mobile money (e.g. MoMo) payments when you choose to use them</li>
                    <li>Send you push notifications (e.g. reminders and important alerts)</li>
                    <li>Improve the app, fix issues, and comply with legal obligations</li>
                    <li>Protect against fraud and unauthorized access</li>
                </ul>

                <hr class="my-8 border-border" />

                <h2 class="mb-3 mt-8 text-lg font-semibold text-foreground">3. Sharing and disclosure</h2>
                <ul class="mb-4 list-inside list-disc space-y-1 text-foreground">
                    <li><strong>Service providers:</strong> We use third-party services that process data on our behalf, including:
                        <ul class="mt-2 list-inside list-disc space-y-1 pl-4 text-muted-foreground">
                            <li><strong>Firebase (Google)</strong> for app infrastructure and push notifications (FCM). Their use of data is governed by <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer" class="text-primary underline underline-offset-4 hover:opacity-90">Google's Privacy Policy</a>.</li>
                            <li><strong>Payment providers</strong> (e.g. for MoMo) when you initiate a payment; their use of data is governed by their own privacy policies.</li>
                        </ul>
                    </li>
                    <li><strong>Legal and safety:</strong> We may disclose your information if required by law, to protect our rights or safety, or to prevent fraud or illegal activity.</li>
                    <li>We do not sell your personal information to third parties.</li>
                </ul>

                <hr class="my-8 border-border" />

                <h2 class="mb-3 mt-8 text-lg font-semibold text-foreground">4. Data storage and security</h2>
                <ul class="mb-8 list-inside list-disc space-y-1 text-foreground">
                    <li>Your data is sent to our servers over encrypted connections (HTTPS).</li>
                    <li>We use industry-standard measures to protect your data on our systems.</li>
                    <li>Login and sensitive data stored on your device are kept in app-private storage. If you enable local authentication (biometrics/device lock), access to the app is further protected by your device.</li>
                </ul>

                <hr class="my-8 border-border" />

                <h2 class="mb-3 mt-8 text-lg font-semibold text-foreground">5. Data retention</h2>
                <ul class="mb-8 list-inside list-disc space-y-1 text-foreground">
                    <li>We retain your account and transaction data for as long as your account is active and as needed to provide the service and comply with legal, tax, or regulatory requirements.</li>
                    <li>If you delete your account, we will delete or anonymize your personal data in line with our retention and legal obligations.</li>
                </ul>

                <hr class="my-8 border-border" />

                <h2 class="mb-3 mt-8 text-lg font-semibold text-foreground">6. Your rights and choices</h2>
                <ul class="mb-8 list-inside list-disc space-y-1 text-foreground">
                    <li><strong>Access and correction:</strong> You can view and update your account details (e.g. name, email, phone) in the app or by contacting us.</li>
                    <li><strong>Notifications:</strong> You can disable push notifications in your device settings.</li>
                    <li><strong>Local authentication:</strong> You can turn biometric/device lock on or off in the app settings.</li>
                    <li><strong>Account deletion:</strong> You may request deletion of your account and associated data by contacting us (see Section 8).</li>
                    <li>Depending on your location, you may have additional rights (e.g. access, portability, erasure, objection, restriction) under applicable data protection laws.</li>
                </ul>

                <hr class="my-8 border-border" />

                <h2 class="mb-3 mt-8 text-lg font-semibold text-foreground">7. Children</h2>
                <p class="mb-8 text-foreground">
                    Pesa Track is not intended for users under the age of 16. We do not knowingly collect personal information from children. If you believe we have collected such information, please contact us so we can delete it.
                </p>

                <hr class="my-8 border-border" />

                <h2 class="mb-3 mt-8 text-lg font-semibold text-foreground">8. Contact us</h2>
                <p class="mb-3 text-foreground">
                    For privacy-related questions, to exercise your rights, or to request account deletion, contact us at:
                </p>
                <ul class="mb-8 list-inside list-disc space-y-1 text-foreground">
                    <li><strong>Email:</strong> [Your support/privacy email]</li>
                    <li><strong>App / website:</strong> [Your contact or support page]</li>
                </ul>
                <p class="mb-8 text-foreground">We will respond to legitimate requests in line with applicable law.</p>

                <hr class="my-8 border-border" />

                <h2 class="mb-3 mt-8 text-lg font-semibold text-foreground">9. Changes to this policy</h2>
                <p class="mb-8 text-foreground">
                    We may update this Privacy Policy from time to time. We will notify you of material changes through the app or by email where appropriate. The "Last updated" date at the top indicates when the policy was last revised. Continued use of Pesa Track after changes constitutes acceptance of the updated policy.
                </p>

                <hr class="my-8 border-border" />

                <p class="text-sm italic text-muted-foreground">
                    This privacy policy applies to the Pesa Track mobile application and services provided at https://pesatrack.app and related APIs.
                </p>
            </article>
        </main>
    </div>
</template>
