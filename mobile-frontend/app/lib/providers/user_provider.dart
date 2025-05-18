import 'package:flutter/material.dart';
import '../models/user.dart';
import '../services/user_api.dart';

class UserProvider with ChangeNotifier {
  List<User> _users = [];
  User? _user;
  bool _isLoading = false;
  String? _error;

  List<User> get users => _users;
  User? get user => _user;
  bool get isLoading => _isLoading;
  String? get error => _error;

  final UserApi _userApi = UserApi();

  Future<void> fetchUsers() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      _users = await _userApi.fetchUsers();
    } catch (e) {
      _error = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchCurrentUser() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      _user = await _userApi.fetchCurrentUser();
    } catch (e) {
      _error = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> updateUser({
    required String name,
    required String phone,
    required String email,
    required String address,
  }) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      await _userApi.updateUser(
        name: name,
        phone: phone,
        email: email,
        address: address,
      );

      // Fetch fresh data after update
      await fetchCurrentUser();
    } catch (e) {
      // _error = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}