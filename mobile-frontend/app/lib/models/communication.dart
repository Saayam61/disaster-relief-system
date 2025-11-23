class Communication {
  final String sender_id;
  final String receiver_id;
  final String message;
  final DateTime timestamp;
  final String read_status;

  Communication({
    required this.sender_id,
    required this.receiver_id,
    required this.message,
    required this.timestamp,
    required this.read_status,
  });


  factory Communication.fromJson(Map<String, dynamic> json) {
    return Communication(
      sender_id: json['sender_id'],
      receiver_id: json['receiver_id'],
      message: json['message'],
      timestamp: DateTime.parse(json['timestamp']),
      read_status: json['read_status'],
    );
  }
}

String timeAgo(dynamic date) {
  DateTime dateTime;

  if (date is String) {
    dateTime = DateTime.parse(date); // parse ISO timestamp string
  } else if (date is DateTime) {
    dateTime = date;
  } else {
    throw ArgumentError('timeAgo only accepts String or DateTime');
  }

  final Duration diff = DateTime.now().difference(dateTime);

  if (diff.inSeconds < 60) return 'Just now';
  if (diff.inMinutes < 60) return '${diff.inMinutes}m ago';
  if (diff.inHours < 24) return '${diff.inHours}h ago';
  return '${diff.inDays}d ago';
}


