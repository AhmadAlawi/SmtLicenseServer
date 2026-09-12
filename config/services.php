<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Shared bearer token the provisioning pipeline sends to
    // POST /api/v1/instances/register — never used by a running instance,
    // only by the pipeline that creates new ones.
    'provisioning' => [
        'token' => env('PROVISIONING_TOKEN', ''),
    ],

    // Phase 7 — real Railway auto-provisioning + white-label domains.
    'railway' => [
        'token'          => env('RAILWAY_API_TOKEN', ''),
        'project_id'     => env('RAILWAY_PROJECT_ID', ''),
        'environment_id' => env('RAILWAY_ENVIRONMENT_ID', ''),
        'source_repo'    => env('RAILWAY_SOURCE_REPO', ''),
        'source_branch'  => env('RAILWAY_SOURCE_BRANCH', 'main'),
    ],

    'cloudflare' => [
        'token'   => env('CLOUDFLARE_API_TOKEN', ''),
        'zone_id' => env('CLOUDFLARE_ZONE_ID', ''),
    ],

    // The owner's own domain every tenant's white-label subdomain is
    // carved from: {slug}.{root_domain}. Never Railway's own domain — the
    // whole point of Phase 7 is that Railway is invisible to the customer.
    'platform' => [
        'root_domain' => env('ROOT_DOMAIN', ''),
    ],

    'google_analytics' => [
        'id' => env('GA4_MEASUREMENT_ID'),
    ],

    // Update-push system — this server hosts the public feed every SaasPOS
    // tenant polls (SaasPOS's own updater engine already exists; this is
    // just what feeds it). The secret key never leaves this env var — it
    // signs each release's zip server-side at publish time
    // ({@see \App\Actions\Releases\PublishRelease}) so staff never handle a
    // manual signing step. The public half is the SAME value every tenant
    // gets as POS_UPDATE_PUBLIC_KEY at provisioning time
    // ({@see \App\Jobs\ProvisionInstance}) — changing this key requires
    // re-provisioning every tenant's public key too, or old installs can
    // never verify a new release again.
    'updates' => [
        'signing_secret_key' => env('UPDATE_SIGNING_SECRET_KEY', ''),
        'signing_public_key' => env('UPDATE_SIGNING_PUBLIC_KEY', ''),
    ],

];
