import 'package:intl/intl.dart';

class Contribution {
  final String? userId;
  final String id;
  final DateTime createdAt;
  final String centerName;
  final String? volName;
  final String? orgName;
  final String name;
  final int quantity;
  final String unit;
  final String? description;
  final String type;

  Contribution({
    this.userId,
    required this.id,
    required this.createdAt,
    required this.centerName,
    this.volName,
    this.orgName,
    required this.name,
    required this.quantity,
    required this.unit,
    this.description,
    required this.type,
  });

  factory Contribution.fromJson(Map<String, dynamic> json) {
    return Contribution(
      id: json['contribution_id'].toString(),
      createdAt: DateTime.parse(json['created_at']),
      centerName: json['relief_center']['user']['name'],
      volName: json['volunteer']?['user']?['name'] ?? '-',
      orgName: json['organization']?['user']?['name'] ?? '-',
      name: json['name'],
      quantity: json['quantity'],
      unit: json['unit'],
      description: json['description'],
      type: json['type'],
    );
  }
}

String getDate(DateTime datetime) {
  try {
    return DateFormat('yyyy-MM-dd').format(datetime);
  } catch (e) {
    return 'Invalid date';
  }
}

String getType(String type) {
  if (type == 'received') {
    return 'Donated';
  } else {
    return 'Received';
  }
}
