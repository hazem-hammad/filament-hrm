<?php

use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

/**
 * Create a standardized FCM (Firebase Cloud Messaging) notification message
 * 
 * This helper function constructs a cross-platform push notification that works
 * consistently across Android and iOS devices. It handles platform-specific
 * notification formats and provides a unified interface for sending push notifications.
 * 
 * Use Cases:
 * - Send booking confirmations: fcmMessage('Booking Confirmed', 'Your session with Dr. Smith is confirmed')
 * - Send appointment reminders: fcmMessage('Reminder', 'Your appointment starts in 30 minutes', 'appointment', '123')
 * - Send payment notifications: fcmMessage('Payment Received', 'Your payment of $50 was successful')
 * - Send system alerts: fcmMessage('Account Updated', 'Your profile information has been updated')
 * - Send chat messages: fcmMessage('New Message', 'You have a new message from Dr. Smith', 'chat', 'conv_456')
 * 
 * @param string $title The notification title (appears prominently in notification)
 * @param string $body The notification body text (detailed message content)
 * @param string|null $actionType Optional action type for handling notification taps (e.g., 'appointment', 'chat', 'payment')
 * @param string|null $actionId Optional ID associated with the action (e.g., appointment ID, conversation ID)
 * @param array $extraData Additional custom data to include in the notification payload
 * @param int|null $badge Badge count for iOS app icon (defaults to 1)
 * @param string|null $sound Sound to play when notification arrives (defaults to 'default')
 * 
 * @return FcmMessage Configured FCM message ready to be sent
 * @throws \InvalidArgumentException When title or body are empty
 */
function fcmMessage(
    string $title,
    string $body,
    ?string $actionType = null,
    ?string $actionId = null,
    array $extraData = [],
    ?int $badge = 1,
    ?string $sound = 'default'
): FcmMessage {
    // Validate input parameters to ensure notification has required content
    if (empty($title) || empty($body)) {
        throw new \InvalidArgumentException('Title and body cannot be empty');
    }

    // Prepare base notification data with platform-specific configurations
    // This structure ensures consistent behavior across Android and iOS devices
    $baseNotificationData = [
        // TODO: Uncomment when implementing deep linking functionality
        // This would allow the app to navigate to specific screens when notification is tapped
        //        'action' => [
        //            'id' => $actionId ?? '',     // Specific resource ID (appointment ID, message ID, etc.)
        //            'type' => $actionType ?? '', // Action type for routing (appointment, chat, payment, etc.)
        //        ],
        
        // Android-specific notification configuration
        // Controls how notifications appear and behave on Android devices
        'android' => [
            'notification' => [
                'sound' => $sound,      // Sound file to play (default uses system notification sound)
                'title' => $title,      // Notification title shown in notification drawer
                'body' => $body,        // Notification body text shown below title
            ],
        ],
        
        // Apple Push Notification Service (APNS) configuration for iOS devices
        // Follows Apple's specific payload structure requirements
        'apns' => [
            'payload' => [
                'aps' => [
                    'sound' => $sound,              // Sound to play on iOS device
                    'badge' => $badge,              // Number to display on app icon badge
                    'alert' => [
                        'title' => $title,          // Notification title for iOS
                        'body' => $body,            // Notification body for iOS
                    ],
                ],
            ],
        ],
    ];

    // Merge any additional custom data with base configuration
    // This allows overriding default values or adding extra payload data
    // Uses array_replace_recursive to maintain nested structure while allowing overrides
    $customData = array_replace_recursive($baseNotificationData, $extraData);

    // Create the final FCM message object combining notification display and custom data
    // The notification parameter handles the basic display (title/body)
    // The custom() method adds platform-specific configurations and extra data
    return (new FcmMessage(
        notification: new FcmNotification(
            title: $title,
            body: $body
        )
    ))->custom($customData);
}
