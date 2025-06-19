
class Volunteer {
  final int id;
  final String volName;
  final String volAddress;

  Volunteer({required this.id, required this.volName, required this.volAddress});

  factory Volunteer.fromJson(Map<String, dynamic> json) {
    return Volunteer(
      id :json['user_id'],
      volName: json['user']['name'],
      volAddress: json['user']['address']
    );
  }
}