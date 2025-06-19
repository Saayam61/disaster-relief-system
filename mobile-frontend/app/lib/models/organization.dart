class Organization {
  final String id;
  final String centerName;
  final String orgAddress;

  Organization({
    required this.id,
    required this.centerName,
    required this.orgAddress,
  });

  factory Organization.fromJson(Map<String, dynamic> json) {
    return Organization(
      id: json['center_id'].toString(),
      centerName: json['user']['name'],
      orgAddress: json['user']['address'],
    );
  }
}


