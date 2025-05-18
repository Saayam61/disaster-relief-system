class ReliefCenter {
  final String id;
  final String centerName;

  ReliefCenter({
    required this.id,
    required this.centerName,
  });

  factory ReliefCenter.fromJson(Map<String, dynamic> json) {
    return ReliefCenter(
      id: json['center_id'].toString(),
      centerName: json['user']['name'],
    );
  }
}


