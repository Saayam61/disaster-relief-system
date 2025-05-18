class NewsFeed {
  final String title;
  final String content;
  final String? imageUrl;
  final DateTime createdAt;
  final String centerName;
  final String centerAddress;

  NewsFeed({
    required this.title,
    required this.content,
    this.imageUrl,
    required this.createdAt,
    required this.centerName,
    required this.centerAddress,
  });


  factory NewsFeed.fromJson(Map<String, dynamic> json) {
    return NewsFeed(
      title: json['title'],
      content: json['content'],
      imageUrl: json['image_url'],
      createdAt: DateTime.parse(json['created_at']),
      centerName: json['relief_center']['user']['name'],
      centerAddress: json['relief_center']['user']['address'],
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

