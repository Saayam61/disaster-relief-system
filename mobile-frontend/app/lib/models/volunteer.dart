
class Volunteer {
  // final int id;
  // final String volName;
  // final String volAddress;
  final String? centerName;
  final String? orgName;
  final String approvalStatus;
  final String? skills;
  final String? availability;
  final String status;

  Volunteer({
    // required this.id, 
    // required this.volName, 
    // required this.volAddress,
    this.centerName,
    this.orgName,
    required this.approvalStatus,
    this.skills,
    this.availability,
    required this.status,
  });

  factory Volunteer.fromJson(Map<String, dynamic> json) {
    return Volunteer(
      // id :json['user_id'] ?? 0,
      // volName: json['user']?['name'] ?? '',
      // volAddress: json['user']?['address'] ?? '',
    centerName: json['relief_center'] != null ? json['relief_center']['user']['name'] ?? '' : '',
    orgName: json['organization'] != null ? json['organization']['user']['name'] ?? '' : '',
      approvalStatus: json['approval_status'],
      skills: json['skills'] ?? null,
      availability: json['availability'] ?? null,
      status: json['status'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'center_name': centerName,
      'org_name': orgName,
      'approval_status': approvalStatus,
      'skills': skills,
      'availability': availability,
      'status': status,
    };
  }
}

final Map<String, String> statusMap = {
  'Active': 'active',
  'Inactive': 'inactive'
};