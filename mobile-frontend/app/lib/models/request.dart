import 'package:intl/intl.dart';

class Request {
  final String id;
  final DateTime createdAt;
  final String centerName;
  final String type;
  final String status;
  final String description;
  final int? quantity;
  final String? unit;
  final String urgency;

  Request({
    required this.id,
    required this.createdAt,
    required this.centerName,
    required this.type,
    required this.status,
    required this.description,
    this.quantity,
    this.unit,
    required this.urgency,
  });

  factory Request.fromJson(Map<String, dynamic> json) {
    return Request(
      id: json['request_id'].toString(),
      createdAt: DateTime.parse(json['created_at']),
      centerName: json['relief_center']['user']['name'],
      type: json['request_type'],
      status: json['status'],
      description: json['description'],
      quantity: json['quantity'] ?? null,
      unit: json['unit'] ?? null,
      urgency: json['urgency'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'request_id': id,
      'description': description,
      'quantity': quantity,
      'unit': unit,
      'status': status,
      'request_type': type,
      'urgency': urgency,
      'center_id': centerName,
    };
  }
}

String getDate(DateTime datetime) {
  try {
    return DateFormat('yyyy-MM-dd').format(datetime);
  } catch (e) {
    return 'Invalid date';
  }
}

final Map<String, String> typeMap = {
  'Supply': 'supply',
  'Evacuation': 'evacuation',
  'Medical': 'medical',
  'Other': 'other',
};

final Map<String, String> urgencyMap = {
  'Low': 'low',
  'Medium': 'medium',
  'High': 'high',
};


