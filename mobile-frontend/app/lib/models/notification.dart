class AppNotification {
  final String id;
  final String? message;
  final DateTime timestamp;

  AppNotification({required this.id, this.message, required this.timestamp});

  factory AppNotification.fromJson(Map<String, dynamic> json) {
    return AppNotification(
      id: json['id'],
      message: json['data']['message'], // because it's wrapped in 'data'
      timestamp: DateTime.parse(json['created_at']),
    );
  }
}

String timeAgo(DateTime dateTime) {
  final Duration diff = DateTime.now().difference(dateTime);
  if (diff.inSeconds < 60) return 'Just now';
  if (diff.inMinutes < 60) return '${diff.inMinutes}m ago';
  if (diff.inHours < 24) return '${diff.inHours}h ago';
  return '${diff.inDays}d ago';
}

