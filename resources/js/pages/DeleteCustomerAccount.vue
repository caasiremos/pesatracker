<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const form = useForm({
    email: '',
});

const submit = () => {
    form.clearErrors();
    if (!form.email.trim()) {
        form.setError('email', 'Please enter your email address.');
        return;
    }
    const email = form.email;

    Swal.fire({
        title: 'Delete your account?',
        text: 'This action cannot be undone. All your data will be permanently removed.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete my account',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            form.post(route('delete-customer-account.submit'), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Request received',
                        text: 'Your account deletion request has been submitted.',
                        icon: 'success',
                        confirmButtonColor: '#2563eb',
                    });
                },
                onError: (errors) => {
                    Swal.fire({
                        title: 'Error',
                        text: errors?.email ?? 'Something went wrong. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#2563eb',
                    });
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Delete customer account" />

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
            <div class="space-y-8">
                <div>
                    <h1 class="mb-2 text-2xl font-semibold tracking-tight text-foreground sm:text-3xl">
                        Delete your account
                    </h1>
                    <p class="text-muted-foreground">
                        Enter the email address associated with your account. After you confirm, we will process your
                        deletion request.
                    </p>
                </div>

                <form @submit.prevent="submit" class="max-w-md space-y-4">
                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            name="email"
                            autocomplete="email"
                            placeholder="email@example.com"
                            :disabled="form.processing"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <Button type="submit" variant="destructive" :disabled="form.processing">
                        {{ form.processing ? 'Submitting…' : 'Request account deletion' }}
                    </Button>
                </form>
            </div>
        </main>
    </div>
</template>
