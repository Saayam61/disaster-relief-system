import 'package:app/models/contribution.dart';

class User {
  final int userId;
  final String name;
  final String email;
  final String phone;
  final String address;
  final String role;
  final double latitude;
  final double longitude;
  final List<Contribution>? contribution;

  User({
    required this.userId,
    required this.name,
    required this.email,
    required this.phone,
    required this.address,
    required this.role,
    required this.latitude,
    required this.longitude,
    this.contribution,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      userId: json['user_id'],
      name: json['name'],
      email: json['email'],
      phone: json['phone'],
      address: json['address'],
      role: json['role'],
      latitude: (json['latitude'] as num).toDouble(),
      longitude: (json['longitude'] as num).toDouble(),
      contribution: json['contributions'] != null
          ? (json['contributions'] as List)
              .map((c) => Contribution.fromJson(c))
              .toList()
          : null, // 🛡️ Only parse if it exists
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'user_id': userId,
      'name': name,
      'email': email,
      'phone': phone,
      'address': address,
      'role': role,
      'latitude': latitude,
      'longitude': longitude,
    };
  }
}
