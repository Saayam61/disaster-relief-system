class ReliefCenter {
  final String id;
  final String centerName;
  final String centerAddress;

  ReliefCenter({
    required this.id,
    required this.centerName,
    required this.centerAddress,
  });

  factory ReliefCenter.fromJson(Map<String, dynamic> json) {
    return ReliefCenter(
      id: json['center_id'].toString(),
      centerName: json['user']['name'],
      centerAddress: json['user']['address'],
    );
  }
}


