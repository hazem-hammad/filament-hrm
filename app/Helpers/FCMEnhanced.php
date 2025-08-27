<?php

use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use Illuminate\Support\Facades\Log;

/**
 * Enhanced FCM helper with improved error handling, validation, and features
 */

/**
 * Create an enhanced FCM message with priority, TTL, and deep linking support
 *
 * @param string $title Notification title
 * @param string $body Notification body
 * @param string|null $actionType Action type for deep linking
 * @param string|null $actionId Action ID for deep linking
 * @param array $extraData Additional custom data
 * @param int|null $badge iOS badge count
 * @param string $sound Sound to play
 * @param string $priority Message priority ('high', 'normal')
 * @param int $ttlSeconds Time to live in seconds (default: 1 hour)
 * @param string|null $imageUrl URL for notification image
 * @return FcmMessage
 */
function fcmMessageEnhanced(
    string $title,
    string $body,
    ?string $actionType = null,
    ?string $actionId = null,
    array $extraData = [],
    ?int $badge = 1,
    string $sound = 'default',
    string $priority = 'high',
    int $ttlSeconds = 3600,
    ?string $imageUrl = null
): FcmMessage {
    // Enhanced validation
    if (empty(trim($title)) || empty(trim($body))) {
        throw new \InvalidArgumentException('Title and body cannot be empty or whitespace');
    }
    
    if (strlen($title) > 100) {
        throw new \InvalidArgumentException('Title must be 100 characters or less');
    }
    
    if (strlen($body) > 500) {
        throw new \InvalidArgumentException('Body must be 500 characters or less');
    }

    // Validate priority
    if (!in_array($priority, ['high', 'normal'])) {
        throw new \InvalidArgumentException('Priority must be "high" or "normal"');
    }

    // Log notification creation for debugging
    Log::info('FCM notification created', [
        'title' => $title,
        'action_type' => $actionType,
        'action_id' => $actionId,
        'priority' => $priority,
        'ttl' => $ttlSeconds
    ]);

    // Build notification object
    $notification = new FcmNotification(
        title: $title,
        body: $body,
        image: $imageUrl
    );

    // Enhanced Android configuration
    $androidConfig = [
        'priority' => $priority,
        'ttl' => $ttlSeconds . 's',
        'notification' => [
            'title' => $title,
            'body' => $body,
            'sound' => $sound,
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'channel_id' => 'congora_notifications',
            'priority' => $priority === 'high' ? 'high' : 'default',
            'image' => $imageUrl,
        ]
    ];

    // Enhanced iOS configuration  
    $apnsConfig = [
        'headers' => [
            'apns-priority' => $priority === 'high' ? '10' : '5',
            'apns-expiration' => (string)(time() + $ttlSeconds),
        ],
        'payload' => [
            'aps' => [
                'alert' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'sound' => $sound,
                'badge' => $badge,
                'category' => $actionType,
                'mutable-content' => 1,
            ]
        ]
    ];

    // Add deep linking data if provided
    $customData = [];
    if ($actionType && $actionId) {
        $customData['action'] = [
            'type' => $actionType,
            'id' => $actionId,
        ];
        
        // Add to platform-specific configs
        $androidConfig['data'] = $customData;
        $apnsConfig['payload']['custom_data'] = $customData;
    }

    // Merge with any additional custom data
    $finalData = array_merge_recursive([
        'android' => $androidConfig,
        'apns' => $apnsConfig,
    ], $extraData);

    return (new FcmMessage(notification: $notification))->custom($finalData);
}

/**
 * Create a high-priority notification for urgent messages
 */
function fcmUrgentMessage(
    string $title,
    string $body,
    ?string $actionType = null,
    ?string $actionId = null,
    array $extraData = []
): FcmMessage {
    return fcmMessageEnhanced(
        title: $title,
        body: $body,
        actionType: $actionType,
        actionId: $actionId,
        extraData: $extraData,
        priority: 'high',
        sound: 'urgent_notification.wav',
        ttlSeconds: 300 // 5 minutes for urgent messages
    );
}

/**
 * Create a silent notification for background updates
 */
function fcmSilentMessage(
    array $data,
    int $ttlSeconds = 3600
): FcmMessage {
    return (new FcmMessage())
        ->custom([
            'android' => [
                'priority' => 'high',
                'data' => $data,
            ],
            'apns' => [
                'headers' => [
                    'apns-priority' => '5',
                    'apns-push-type' => 'background',
                ],
                'payload' => [
                    'aps' => [
                        'content-available' => 1,
                    ],
                    'data' => $data,
                ]
            ]
        ]);
}