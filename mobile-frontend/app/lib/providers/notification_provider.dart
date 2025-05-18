import 'package:flutter/material.dart';
import '../models/notification.dart';
import '../services/notification_api.dart';

class NotificationProvider with ChangeNotifier {
  List<AppNotification> _unreadNotifications = [];
  bool _isLoading = false;
  String? _error;

  List<AppNotification> get unreadNotifications => _unreadNotifications;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<void> loadNotifications() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      _unreadNotifications = await NotificationApi.fetchNotifications(); // this should only fetch unread from backend
    } catch (e) {
      _error = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  int get unreadCount => _unreadNotifications.length;

  Future<void> markAsRead(String id) async {
    try {
      await NotificationApi.markNotificationAsRead(id);
      _unreadNotifications.removeWhere((notification) => notification.id == id);
      notifyListeners();
    } catch (e) {
      print("Failed to mark notification as read: $e");
    }
  }
}
